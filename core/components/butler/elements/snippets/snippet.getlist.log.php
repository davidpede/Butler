<?php
/**
 * Get list log messages
 *
 * @package Butler
 * @subpackage snippets
 */

/* SETTINGS */
$run_id = !empty($run_id) ? $run_id : '';
$rowTpl = !empty($rowTpl) ? $rowTpl : '';
$where = !empty($where) ? $where : '';

//$modx->setDebug(true);
$modx->getService('butler','Butler',$modx->getOption('butler.core_path',null,$modx->getOption('core_path').'components/butler/').'model/butler/',$scriptProperties);
if (!($modx->butler instanceof Butler)) return $modx->log(MODX::LOG_LEVEL_ERROR, 'Service class not loaded');

//log query
$query = $modx->newQuery('ButlerLog');
$query->where(array(
  'run_id' => $run_id
));

$query->select(array('ButlerLog.*'));

if ($query->prepare() && $query->stmt->execute()) {
  $logs = $query->stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($logs as $msg) {
    $output .= $modx->parseChunk($rowTpl,$msg);
  }

}

return $output;