<?php
/**
 * Filesystem scanner task
 *
 * @property int $task_id
 * @property array $args
 * @property string $args['path']
 *
 * @package butler
 */
class Scan {

  /** @var modX $modx */
  private $modx;

  public $task_id;
  private $path;

  public function __construct(modX &$modx) {
    $this->modx =& $modx;
    $this->butler = $this->modx->getService('butler','Butler',$this->modx->getOption('butler.core_path',null,$this->modx->getOption('core_path').'components/butler/').'model/butler/');
  }

  public function getLastScan($task_id) {
    $query = $this->modx->newQuery('ButlerTasklog');
    $query->where(array(
      'task_id' => $task_id,
    ));
    $query->select('stamp');
    $query->sortby('stamp','DESC');
    $query->limit(1);
    if ($query->prepare() && $query->stmt->execute()) {
      //$baseline_array = $query->stmt->fetchAll(PDO::FETCH_ASSOC);

      //Bind to variable by column name
      $query->stmt->bindColumn('stamp', $stamp);

      while ($row = $query->stmt->fetch(PDO::FETCH_ASSOC)) {
        $response = $stamp;
        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Label: ' . print_r($row, true));
      }
    }
    return $response;
  }

  public function getBaseline($task_id) {
    $query = $this->modx->newQuery('ButlerBaseline');
    $query->where(array(
      'task_id' => $task_id,
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

  public function run($task_id,$args) {
    $start = microtime(true);
    $stamp = date('Y-m-d H:i:s');
    //Basic checks
    if (file_exists($args['path'])) {
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

      $dir = new RecursiveDirectoryIterator($args['path']);
      $iter = new RecursiveIteratorIterator($dir);

      $baseline = $this->getBaseline($task_id);
      if (!$baseline) { $baseline = array(); }

      $last_run = $this->getLastScan($task_id);
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
                'task_id' => $task_id
              ));
              $file->save();
              //LOG new file EXCEPT if $firstscan
              if(!$firstscan) {
                $log = $this->modx->newObject('ButlerScanlog');
                $log->fromArray(array(
                  'stamp' => $stamp,
                  'status' => 'NEW',
                  'file_name' => $new[$file_path]['file_name'],
                  'file_path' => $file_path,
                  'file_hash_new' => $new[$file_path]['file_hash'],
                  'file_last_mod' => $new[$file_path]['file_last_mod'],
                  'task_id' => $task_id
                ));
                $log->save();
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
                  'task_id' => $task_id,
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
                $log = $this->modx->newObject('ButlerScanlog');
                $log->fromArray(array(
                  'stamp' => $stamp,
                  'status' => 'MODIFIED',
                  'file_name' => $modified[$file_path]['file_name'],
                  'file_path' => $file_path,
                  'file_hash_new' => $modified[$file_path]['file_hash_new'],
                  'file_hash_org' => $modified[$file_path]['file_hash_org'],
                  'file_last_mod' => $modified[$file_path]['file_last_mod'],
                  'task_id' => $task_id
                ));
                $log->save();
              }
            }
          }
        }
        $iter->next();
      }

      //IF file was DELETED
      $deleted = array_diff_key($baseline, $current);
      $deleted = str_replace(chr(92),chr(47),$deleted); //Ensure path without \'s

      //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Label: ' . print_r($baseline, true));

      foreach($deleted as $key => $value)
      {
        //REMOVE deleted file record in baseline
        $row = $this->modx->getObject('ButlerBaseline', array(
          'task_id' => $task_id,
          'file_path' => $key
        ));
        if ($row) {
          $row->remove();
        }
        //LOG deleted file
        $log = $this->modx->newObject('ButlerScanlog');
        $log->fromArray(array(
          'stamp' => $stamp,
          'status' => 'DELETED',
          'file_name' => $deleted[$key]['file_name'],
          'file_path' => $key,
          'file_hash_org' => $deleted[$key]['file_hash'],
          'file_last_mod' => $deleted[$key]['file_last_mod'],
          'task_id' => $task_id
        ));
        $log->save();
      }

      //PREPARE Log
      $file_count = count($current);
      $count_new = count($new);
      $count_modified = count($modified);
      $count_deleted = count($deleted);
      $total_changes = $count_new + $count_modified + $count_deleted;

      //log msg
      $msg = ' New: ' . $count_new . ' Modified: ' . $count_modified . ' Deleted: ' . $count_deleted . ' Scanned: ' . $file_count;
      if ($firstscan) {
        $msg = 'Files scanned: ' . $file_count . '. First scan of directory, nothing to compare';
      }
    } else {
      $msg = 'Scan directory not found: ' . $path;
    }

    $duration = round(microtime(true) - $start, 5);

    //LOG task execution
    $this->butler->logTask($stamp,$task_id,$msg,$duration);

    //test
    //print_r($this->butler->hello('Talk to me now!!' . $duration));

    //echo "<pre>";
    //print_r($current);
    //echo "</pre>";
  }
}
?>