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
   * @required $task['task_id']
   * @required $task['task_key']
   *
   * @return $this
   */
  public function runTask($task) {
    //Init
    $butler = $this->modx->getService($task['task_type'],'butler.tasks.'.$task['task_type'],$this->config['modelPath']);
    $run = $this->startRun($task);
    //Start the run
    if ($run && $butler instanceof $task['task_type']) {
      $timer = microtime(true);
      $run->set('task_status', 'ACTIVE');
      $run->set('start', date('Y-m-d H:i:s'));
      $run->save();
      //Run the task
      $task['run_id'] = $run->get('id');
      $return = $butler->run($task);
      //Done - log status after error check
      if ($return) {
        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Run: ' . print_r($run->toArray(), true));
        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Task: ' . print_r($task, true));
        $run->set('task_status', 'SUCCESS');
        $run->save();
        //Notifier
        if ($task['task_notify'] == 1) {
          $notifier = $this->modx->getService('notifier','butler.Notifier',$this->config['modelPath']);
          //run notifier
          $return = $notifier->run($task);
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
          ),$task);
          $run->set('notifier_status', 'DISABLED');
        }
      } else {
        $run->set('task_status', 'FAILED');
      }
      $run->set('finish', date('Y-m-d H:i:s'));
      $run->set('duration',round(microtime(true) - $timer, 5));
      $run->save();
    } else {
      $this->modx->log(xPDO::LOG_LEVEL_ERROR,'runTask() FAILED','','Butler - Task ' . $task['task_id']);
    }
    return false;
  }
  /**
   * Create a task execution in ButlerRunlog
   *
   * @param array $task - Array of values to be set
   * @required $task['task_id']
   *
   * @return object $run
   */
  public function startRun($task) {
    $run = $this->modx->newObject('ButlerRunlog',$task);
    if ($run->save()) {
      return $run;
    }
    return false;
  }
  /**
   * Update a task execution in ButlerRunlog
   *
   * @param int $run_id - ID of Runlog to update
   * @param array $data - Array of values to be set
   * @required $run_id
   *
   * @return bool
   */
  public function updateRun($data,$run_id) {
    $run = $this->modx->getObject('ButlerRunlog',$run_id);
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
   * @param array $task - Array of values to be set related to the task
   * @required $task['task_id']
   * @required $task['run_id']
   *
   * @return bool
   */
  public function logMsg($msg,$task) {
    $details = array_merge($msg,$task);
    $log = $this->modx->newObject('ButlerLog',$details);
    $log->set('stamp', date('Y-m-d H:i:s'));
    if ($log->save()) {
      return true;
    }
    return false;
  }
}
?>