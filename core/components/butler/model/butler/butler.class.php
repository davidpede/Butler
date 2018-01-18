<?php
/**
 * The main Butler service class.
 *
 * @package butler
 */
class Butler {
  public $modx;
  public $config = array();
  function __construct(modX &$modx,array $config = array()) {
    $this->modx =& $modx;
    $basePath = $this->modx->getOption('butler.core_path',$config,$this->modx->getOption('core_path').'components/butler/');
    $assetsUrl = $this->modx->getOption('butler.assets_url',$config,$this->modx->getOption('assets_url').'components/butler/');
    $this->config = array_merge(array(
      'basePath' => $basePath,
      'corePath' => $basePath,
      'vendorPath' => $basePath.'vendor/',
      'modelPath' => $basePath.'model/',
      'processorsPath' => $basePath.'processors/',
      'templatesPath' => $basePath.'templates/',
      'chunksPath' => $basePath.'elements/chunks/',
      'jsUrl' => $assetsUrl.'mgr/js/',
      'cssUrl' => $assetsUrl.'mgr/css/',
      'assetsUrl' => $assetsUrl,
      'connectorUrl' => $assetsUrl.'connector.php',
    ),$config);
    $this->modx->addPackage('butler', $this->config['modelPath']);
  }

  /**
   * Start a task execution
   *
   * @param array $task
   * @required $task['id']
   * @required $task['key']
   *
   * @return $this
   */
  public function runTask($task) {
    //Init
    $butler = $this->modx->getService($task['type'],'butler.tasks.'.$task['type'],$this->config['modelPath']);
    $run = $this->startRun($task); //obj
    //Start the run
    if ($run && $butler instanceof $task['type']) {
      $timer = microtime(true);
      $run->set('task_status', 'ACTIVE');
      $run->set('start', date('Y-m-d H:i:s'));
      $run->save();
      //Set the runlog params
      $log = array(
        'task_id' => $task['id'],
        'run_id' => $run->get('id')
      );
      //Run the task
      $return = $butler->run($task,$log);
      if ($return) {
        $run->set('task_status', 'SUCCESS');
        $run->set('duration',round(microtime(true) - $timer, 5));
        $run->save();
        //Notifier
        if ($task['notify'] == 1) {
          $notifier = $this->modx->getService('notifier','butler.Notifier',$this->config['modelPath']);
          //run notifier
          $return = $notifier->run($task,$log);
          if ($return) {
            $run->set('notifier_status', 'SUCCESS');
          } else {
            $run->set('notifier_status', 'FAILED');
          }
        } else {
          $this->logMsg(array(
            'source' => 'NOTIFIER',
            'type' => 'DEBUG',
            'msg' => 'Notifier disabled',
          ),$log);
          $run->set('notifier_status', 'DISABLED');
        }
      } else {
        $run->set('task_status', 'FAILED');
      }
      $run->set('finish', date('Y-m-d H:i:s'));
      $run->save();
    } else {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR,'runTask() FAILED','','Butler - Task ' . $task['id']);
    }
    return false;
  }
  /**
   * Create a task execution in ButlerRunlog
   *
   * @param array $task - Array of values to be set
   * @required $task['id']
   * @required $task['type']
   * @required $task['name']
   *
   * @return object $run
   */
  public function startRun($task) {
    $run = $this->modx->newObject('ButlerRunlog');
    $data = array(
      'task_id' => $task['id'],
      'task_type' => $task['type'],
      'task_name' => $task['name']
    );
    $run->fromArray($data);
    if ($run->save()) {
      return $run;
    }
    return false;
  }
  /**
   * Update a task execution in ButlerRunlog
   *
   * @param array $log - Task and run id numbers
   * @param array $data - Array of values to be set
   * @required $log['run_id']
   *
   * @return bool
   */
  public function updateRun($data,$log) {
    $run = $this->modx->getObject('ButlerRunlog',$log['run_id']);
    if ($data) {
      $run->fromArray($data);
      if ($run->save()) {
        return true;
      }
    }
    return false;
  }
  /**
   * Log a message to ButlerLog
   *
   * @param array $message - Array of values to be set related to the message
   * @param array $log - Task and run id numbers
   * @required $log['task_id']
   * @required $log['run_id']
   *
   * @return bool
   */
  public function logMsg($msg,$log) {
    $entry = $this->modx->newObject('ButlerLog',array_merge($msg,$log));
    $entry->set('stamp', date('Y-m-d H:i:s'));
    if ($entry->save()) {
      return true;
    }
    return false;
  }
}
?>