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

  public function cronTest($msg) {
    $this->modx->log(xPDO::LOG_LEVEL_ERROR,'cronTest() ' . print_r($msg, true));
    return true;
  }

  /**
   * Start a task execution
   *
   * @param string $task_key - Task class name
   * @param int $task_id - Numeric id of the task
   * @param array $args - Array of arguments to pass onto the task
   *
   * @return true.
   */
  public function runTask($task_key,$task_id,$args) {
    //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'runTask() ' . print_r($task_key, true));
    $task = $this->modx->getService('scan','butler.tasks.'.$task_key,$this->config['modelPath']);
    $task->run($task_id,$args);
  }

  /**
   * Log a task execution to the ButlerTaskLog table
   *
   * @param date $stamp - The date stamp for task execution 'Y-m-d H:i:s'
   * @param int $task_id - Numeric id of the task executed
   * @param string $msg - Text to log for the task executed
   *
   * @return true.
   */
  public function logTask($stamp,$task_id,$msg,$duration) {

    //add get task to retrieve name and type

    $log = $this->modx->newObject('ButlerTaskLog');
    $log->fromArray(array(
      'stamp' => $stamp,
      'task_id' => $task_id,
      'msg' => $msg,
      'duration' => $duration
    ));
    $log->save();
    return true;
  }
}
?>