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

$butler->cronTest('Cron ran me');

//Tasks
/*$scheduler->call(function () use ($butler) {
  $butler->cronTest('Scheduler ran me!');
})->at($cron);*/

$cron = '*/10 * * * *';

$scheduler->call(function () use ($butler) {
  $task_key = 'Scan';
  $task_id = 1;
  $args = array(
    'path' => 'C:/xampp/htdocs/repo/revolution/assets/lib/'
  );
  $butler->runTask($task_key,$task_id,$args);
})->at($cron);

$scheduler->call(function () use ($butler) {
  $task_key = 'Scan';
  $task_id = 2;
  $args = array(
    'path' => 'C:/xampp/htdocs/repo/revolution/assets/modx-2.6.0-pl/'
  );
  $butler->runTask($task_key,$task_id,$args);
})->at('*/5 * * * *');

// Let the scheduler execute jobs which are due.
$scheduler->run();

?>