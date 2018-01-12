<?php
/**
 * Filesystem scanner task
 *
 * @property array $task
 *
 * @package butler
 */
class scanFsTask extends Butler {

  public function getLastScan($task) {
    $c = $this->modx->newQuery('ButlerRunlog');
    $c->where(array(
      'task_id' => $task['task_id'],
      'task_status:NOT LIKE' => 'ACTIVE'
    ));
    $c->select('finish');
    $c->sortby('finish','DESC');
    $c->limit(1);
    $result = $this->modx->getObject('ButlerRunlog', $c);
    if ($result) {
      $output = $result->get('finish');
      //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Label: ' . print_r($output, true));
    }
    return $output;
  }

  public function getBaseline($task) {
    $query = $this->modx->newQuery('ButlerBaseline');
    $query->where(array(
      'task_id' => $task['task_id'],
    ));
    $query->select('file_name,file_path,file_hash,file_last_mod');
    $query->sortby('file_path','ASC');
    if ($query->prepare() && $query->stmt->execute()) {
      //$baseline_array = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
      //Bind to variable by column name
      $query->stmt->bindColumn('file_name', $file_name);
      $query->stmt->bindColumn('file_path', $file_path);
      $query->stmt->bindColumn('file_hash', $file_hash);
      $query->stmt->bindColumn('file_last_mod', $file_last_mod);

      while ($row = $query->stmt->fetch(PDO::FETCH_ASSOC)) {
        $response[$row['file_path']] = array(
          'file_name' => $row['file_name'],
          'file_hash' => $row['file_hash'],
          'file_last_mod' => $row['file_last_mod']
        );
        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Label: ' . print_r($row, true));
      }
    }
    return $response;
  }

  public function run($task) {
    $start = microtime(true);
    $stamp = date('Y-m-d H:i:s');
    //Basic checks
    if (file_exists($task['task_path'])) {
      //Init arrays
      $current = array();
      $new = array();
      $modified = array();
      $deleted = array();
      //Init settings
      $inc_ext = array_map('strtolower',array()); //ALLOWED extensions. Empty array will return ALL
      $excl_ext = array_map('strtolower',array()); //EXCLUDED extensions. Empty array will return ALL
      $excl_dir = array(); //EXCLUDED sub-directories. Empty array will return ALL
      $no_ext = true; //Scan extensionless files?

      $dir = new RecursiveDirectoryIterator($task['task_path']);
      $iter = new RecursiveIteratorIterator($dir);

      $baseline = $this->getBaseline($task);
      if (!$baseline) { $baseline = array(); }

      $last_run = $this->getLastScan($task);
      $firstscan = ($last_run) ? false : true;

      //START SCAN
      while ($iter->valid()) {
        // excluded dirs
        if (!$iter->isDot() && !(in_array($iter->getSubPath(), $excl_dir))) {
          //	Get or set file extension ('' vs null)
          if (is_null(pathinfo($iter->key(), PATHINFO_EXTENSION))) {
            $ext = '';
          } else {
            $ext = strtolower(pathinfo($iter->key(), PATHINFO_EXTENSION));
          }
          //Ext checks
          if (
            (in_array($ext, $inc_ext, true)) ||
            // in allowed extension array
            (empty($inc_ext) && !in_array($ext, $excl_ext, true)) ||
            // OR NOT in excluded extension array
            (empty($ext) && $no_ext))
            // OR extensionless AND extensionless is allowed
          {
            $file_path = $iter->key();
            $file_path = str_replace(chr(92),chr(47),$file_path); //Ensure $file_path without \'s

            //CURRENT array
            $current[$file_path] = array(
              'file_hash' => hash_file("sha1", $file_path),
              'file_name' => pathinfo($iter->key(), PATHINFO_BASENAME),
              'file_last_mod' => date("Y-m-d H:i:s", filemtime($file_path))
            );

            //IF file is NEW
            if (!array_key_exists($file_path, $baseline)) {

              //NEW array
              $new[$file_path] = array(
                'file_hash' => $current[$file_path]['file_hash'],
                'file_name' => $current[$file_path]['file_name'],
                'file_last_mod' => $current[$file_path]['file_last_mod']
              );
              //INSERT new file record in baseline
              $file = $this->modx->newObject('ButlerBaseline');
              $file->fromArray(array(
                'file_name' => $new[$file_path]['file_name'],
                'file_path' => $file_path,
                'file_hash' => $new[$file_path]['file_hash'],
                'file_last_mod' => $new[$file_path]['file_last_mod'],
                'task_id' => $task['task_id']
              ));
              $file->save();
              //LOG new file EXCEPT if $firstscan
              if(!$firstscan) {
                $scanlog = $this->modx->newObject('ButlerScanlog');
                $scanlog->fromArray(array(
                  'stamp' => $stamp,
                  'status' => 'NEW',
                  'file_name' => $new[$file_path]['file_name'],
                  'file_path' => $file_path,
                  'file_hash_new' => $new[$file_path]['file_hash'],
                  'file_last_mod' => $new[$file_path]['file_last_mod'],
                  'task_id' => $task['task_id'],
                  'run_id' => $task['run_id']
                ));
                $scanlog->save();
              }
            } else {
              //IF file was MODIFIED
              if ($baseline[$file_path]['file_hash'] <> $current[$file_path]['file_hash'] || $baseline[$file_path]['file_last_mod'] <> $current[$file_path]['file_last_mod']) {

                //MODIFIED array
                $modified[$file_path] = array(
                  'file_hash_org' => $baseline[$file_path]['file_hash'],
                  'file_hash_new' => $current[$file_path]['file_hash'],
                  'file_name' => $current[$file_path]['file_name'],
                  'file_last_mod' => $current[$file_path]['file_last_mod']
                );
                //UPDATE modified file record in baseline
                $row = $this->modx->getObject('ButlerBaseline', array(
                  'task_id' => $task['task_id'],
                  'file_path' => $file_path
                ));
                if ($row) {
                  $row->fromArray(array(
                    'file_name' => $modified[$file_path]['file_name'],
                    'file_hash' => $modified[$file_path]['file_hash_new'],
                    'file_last_mod' => $modified[$file_path]['file_last_mod']
                  ));
                  $row->save();
                }
                //LOG modified file
                $scanlog = $this->modx->newObject('ButlerScanlog');
                $scanlog->fromArray(array(
                  'stamp' => $stamp,
                  'status' => 'MODIFIED',
                  'file_name' => $modified[$file_path]['file_name'],
                  'file_path' => $file_path,
                  'file_hash_new' => $modified[$file_path]['file_hash_new'],
                  'file_hash_org' => $modified[$file_path]['file_hash_org'],
                  'file_last_mod' => $modified[$file_path]['file_last_mod'],
                  'task_id' => $task['task_id'],
                  'run_id' => $task['run_id']
                ));
                $scanlog->save();
              }
            }
          }
        }
        $iter->next();
      }

      //IF file was DELETED
      $deleted = array_diff_key($baseline, $current);
      $deleted = str_replace(chr(92),chr(47),$deleted); //Ensure path without \'s

      foreach($deleted as $key => $value)
      {
        //REMOVE deleted file record in baseline
        $row = $this->modx->getObject('ButlerBaseline', array(
          'task_id' => $task['task_id'],
          'file_path' => $key
        ));
        if ($row) {
          $row->remove();
        }
        //LOG deleted file
        $scanlog = $this->modx->newObject('ButlerScanlog');
        $scanlog->fromArray(array(
          'stamp' => $stamp,
          'status' => 'DELETED',
          'file_name' => $deleted[$key]['file_name'],
          'file_path' => $key,
          'file_hash_org' => $deleted[$key]['file_hash'],
          'file_last_mod' => $deleted[$key]['file_last_mod'],
          'task_id' => $task['task_id'],
          'run_id' => $task['run_id']
        ));
        $scanlog->save();
      }

      //PREPARE Log
      $file_count = count($current);
      $count_new = count($new);
      $count_modified = count($modified);
      $count_deleted = count($deleted);
      $total_changes = $count_new + $count_modified + $count_deleted;
      $duration = round(microtime(true) - $start, 5);

      $this->logMsg(array(
        'msg' => $file_count . ' files scanned',
      ),$task);

      if (!$firstscan) {
        $this->logMsg(array(
          'msg' => $total_changes . ' changes detected',
        ),$task);
        if ($total_changes > 0) {
          $this->logMsg(array(
            'msg' => $count_new . ' new files',
          ),$task);
          $this->logMsg(array(
            'msg' => $count_modified . ' modified files',
          ),$task);
          $this->logMsg(array(
            'msg' => $count_deleted . ' deleted files',
          ),$task);
          //Set notifer flag
          $this->updateRun(array(
            'notify_flag' => 1
          ),$task['run_id']);
        }
      } else {
        $this->logMsg(array(
          'type' => 'DEBUG'
          ,'msg' => 'First scan of directory. Nothing to compare.',
        ),$task);
      }
    } else {
      $this->logMsg(array(
        'type' => 'ERROR',
        'msg' => 'Scan directory not found: ' . $task['task_path'],
      ),$task);
      return false;
    }
    return true;
  }
}
?>