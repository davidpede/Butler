<?php
/**
 * Butler
 *
 * @package butler
 */

//Init MODX
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';
//Init Butler
$butler = $modx->getService('butler','Butler',$modx->getOption('butler.core_path',null,$modx->getOption('core_path').'components/butler/').'model/butler/');
if (!($butler instanceof Butler)) return '';
//Init Scheduler
require_once $butler->config['vendorPath'] . 'autoload.php';
use GO\Scheduler;
$scheduler = new Scheduler();

//Tasks
/*$scheduler->call(function () use ($butler) {
  $butler->cronTest('Scheduler ran me!');
})->at($cron);*/

//Fetch Tasks
$query = $modx->newQuery('ButlerTasks');
$query->where(array(
  'status' => 1
));
$query->select($modx->getSelectColumns('ButlerTasks','ButlerTasks','task_',''));
if ($query->prepare() && $query->stmt->execute()) {
  $tasks = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
}
foreach ($tasks as $task) {
  //$modx->log(xPDO::LOG_LEVEL_ERROR,'Label: ' . print_r($task, true));
  try {
    $scheduler->call(function ($args) use ($butler) {
      $butler->runTask($args);
    },[$task])->at($task['task_cron_exp']);
  } catch (Exception $e) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,print_r($e->getMessage(), true),'','Butler - Task ' . $task['task_id']);
    throw $e;
  }
}

//OPTION ONE
/*$obj = $modx->getObject('ButlerTasklog', array('task_id' => 2));
$task = $obj->toArray();*/

//OPTION TWO
//$task = $modx->getObject('ButlerTasklog', array('task_id' => 2));

//$task = array(
//  'task_id' => 2,
//  'task_key' => 'scanFsTask',
//  'task_name' => 'Resource Lib Scan',
//  'task_path' => 'C:/xampp/htdocs/repo/revolution/assets/lib/',
//  'task_cron_exp' => '*/1 * * * *',
//  'task_notify_id' => 1,
//);
//$scheduler->call(function ($args) use ($butler) {
//  $butler->runTask($args);
//},[$task])->at($task['task_cron_exp']);

// Let the scheduler execute jobs which are due.
$scheduler->run();

?>