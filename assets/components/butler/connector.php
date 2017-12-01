<?php
// the php file which points to the php processors. also restrict access, check access permissions, and 'route' requests to the appropriate processor

define('MODX_REQP', false); //Allow anonymous users

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$webActions = array();

if (in_array($_REQUEST['action'], $webActions)) { //limit web context access to certain processors
  $_SERVER['HTTP_MODAUTH'] = $modx->user->getUserToken($modx->context->get('key'));
}

define('MODX_REQP', true);

$corePath = $modx->getOption('butler.core_path',null,$modx->getOption('core_path').'components/butler/');
require_once $corePath.'model/butler/butler.class.php';

$modx->butler = new Butler($modx);

$path = $modx->getOption('processorsPath',$modx->butler->config,$corePath.'processors/');
$modx->request->handleRequest(array(
  'processors_path' => $path,
  'location' => '',
));
?>