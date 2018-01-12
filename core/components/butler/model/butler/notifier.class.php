<?php
/**
 * Notifier Class
 *
 * @property array $task
 *
 * @package butler
 */
class Notifier extends Butler {

  public function getNotifyTpl($task) {

  }

  public function getUserByGroup($task) {
    $c = $this->modx->newQuery('ButlerRunlog');
    $c->where(array(
      'task_id' => $task['task_id'],
      'status:NOT LIKE' => 'ACTIVE'
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

  public function getUserById($task) {

  }

}
?>