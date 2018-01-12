<?php
/**
 * Notifier Class
 *
 * @property array $task
 *
 * @package butler
 */
class Notifier extends Butler {

  public function run($task) {
    //Fetch notifications
    $query = $this->modx->newQuery('ButlerNotifier');
    $query->where(array(
      'task_id' => $task['task_id']
      ,'status' => 1
    ));
    $query->select($this->modx->getSelectColumns('ButlerNotifier','ButlerNotifier','',''));
    if ($query->prepare() && $query->stmt->execute()) {
      $configs = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
      //Fetch current run object
      $run = $this->modx->getObject('ButlerRunlog',$task['run_id']);
    }

    foreach ($configs as $email) {
      //check flag config
      if ($email['flag'] == 1 && $run->get('notify_flag') !== 1) {
        $this->logMsg(array(
          'source' => 'NOTIFIER',
          'type' => 'DEBUG',
          'msg' => $email['name'] . ' skipped. Notify flag required but not set by task.',
        ),$task);
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,'$email NO FLAG: ' . print_r($email, true));
      } else {
        //send
        $this->modx->log(xPDO::LOG_LEVEL_ERROR,'$email HAS FLAG: ' . print_r($email, true));
      }

      //getEmailAds
      //sendMail
      //log to tasklog
      //error checks
      //log to runlog

    }

    $this->modx->log(xPDO::LOG_LEVEL_ERROR,'Notifier->run(): ' . print_r($run->toArray(), true));
    return true;
  }

  public function getUser($task) {

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

  public function getTpl($task) {

  }

  public function sendEmail($email,$name,$subject,$properties) {

    if (empty($properties['tpl'])) $properties['tpl'] = 'notifierEmailTpl';
    $tpl = $this->getChunk($properties['tpl'],$properties);

    $this->modx->getService('mail', 'mail.modPHPMailer');
    $this->modx->mail->set(modMail::MAIL_BODY, $tpl);
    $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
    $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
    $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('emailsender'));
    $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);

    $this->modx->mail->address('to', $email, $name);
    $this->modx->mail->address('reply-to', $this->modx->getOption('emailsender'));
    $this->modx->mail->setHTML(true);

    $sent = $this->modx->mail->send();
    $this->modx->mail->reset();
    return $sent;
  }
}
?>