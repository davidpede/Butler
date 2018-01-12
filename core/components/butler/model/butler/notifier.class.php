<?php
/**
 * Notifier Class
 *
 * @property array $task
 *
 * @package butler
 */
class Notifier extends Butler {

  public function run($run) {
    //fetch notifications by task id + status
    //foreach notification
    //check flag setting
    //check valid tpl
    //getEmailAds
    //sendMail
    //log to tasklog
    //error checks
    //log to runlog
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
    
    if (empty($properties['tpl'])) $properties['tpl'] = '';
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