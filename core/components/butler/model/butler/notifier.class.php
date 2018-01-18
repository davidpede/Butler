<?php
/**
 * Notifier Class
 *
 * @property array $task
 *
 * @package butler
 */
class Notifier extends Butler {

  public function run($task,$log) {
    //Fetch notifications
    $query = $this->modx->newQuery('ButlerAlerts');
    $query->where(array(
      'task_id' => $log['task_id']
      ,'status' => 1
    ));
    $query->select($this->modx->getSelectColumns('ButlerAlerts','ButlerAlerts','',''));
    if ($query->prepare() && $query->stmt->execute()) {
      $configs = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
      //Fetch current run object
      $run = $this->modx->getObject('ButlerRunlog',$log['run_id']);

      foreach ($configs as $email) {
        //check flag config
        if ($email['flag'] == 1 && $run->get('notify_flag') !== 1) {
          $this->logMsg(array(
            'source' => 'NOTIFIER',
            'type' => 'DEBUG',
            'msg' => $email['name'] . ' skipped. Notify flag required but not set by task.',
          ),$log);
        } else {
          $users = $this->getUsers($email);
          if ($users) {
            $response = true;
            foreach ($users as $user) {
              //send
              if (!$response) return false;
              $response = $this->send($user,$email,$task,$run->toArray(),$log);
            }
          } else {
            $this->logMsg(array(
              'source' => 'NOTIFIER',
              'type' => 'DEBUG',
              'msg' => $email['name'] . ' skipped. No recipients found.',
            ),$log);
          }
        }
      }
    } else {
      $this->logMsg(array(
        'source' => 'NOTIFIER',
        'type' => 'ERROR',
        'msg' => $task['name'] . ' failed. No alert templates found.',
      ),$log);
      return false;
    }
    //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Notifier->run(): ' . print_r($run->toArray(), true));
    return true;
  }

  public function getUsers($config) {
    $query = $this->modx->newQuery('modUser');
    $query->innerJoin('modUserProfile','Profile');
    //where defaults
    $query->where(array(
      'active' => 1,
      'Profile.blocked' => 0
    ));
    //by usergroup
    if($config['usergroups']) {
      $usergroups = is_array($config['usergroups']) ? $config['usergroups'] : explode(',',$config['usergroups']);
      $query->where(array('primary_group:IN' => $usergroups));
    }
    //by id
    if($config['users']) {
      $ids = is_array($config['users']) ? $config['users'] : explode(',',$config['users']);
      $query->where(array('id:IN' => $ids),xPDOQuery::SQL_OR);
    }

    $query->select(array('modUser.id,modUser.username,modUser.active,modUser.primary_group'));
    $query->select($this->modx->getSelectColumns('modUserProfile','Profile','profile_',array('fullname','email','blocked')));

    if ($query->prepare() && $query->stmt->execute()) {
      return $query->stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  }

  public function send($user,$alert,$task,$run,$log) {
    if (empty($alert['tpl'])) $alert['tpl'] = 'notifierEmailTpl';
    $properties = array(
      'user' => $user,
      'alert' => $alert,
      'task' => $task,
      'run' => $run
    );
    $tpl = $this->modx->getChunk($alert['tpl'],$properties);
    if (!$tpl) {
      $this->logMsg(array(
        'source' => 'NOTIFIER',
        'type' => 'ERROR',
        'msg' => $alert['tpl'] . ' chunk not found.',
      ),$log);
      return false;
    }

    $this->modx->getService('mail', 'mail.modPHPMailer');
    $this->modx->mail->set(modMail::MAIL_BODY, $tpl);
    $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
    $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
    $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('emailsender'));
    $this->modx->mail->set(modMail::MAIL_SUBJECT, $alert['subject']);
    $this->modx->mail->address('to', $user['profile_email'], $user['profile_fullname']);
    $this->modx->mail->address('reply-to', $this->modx->getOption('emailsender'));
    $this->modx->mail->setHTML(true);

    if (!$this->modx->mail->send()) {
      $this->logMsg(array(
        'source' => 'NOTIFIER',
        'type' => 'ERROR',
        'msg' => $this->modx->mail->mailer->ErrorInfo,
      ),$log);
    }

    $this->modx->mail->reset();
    //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'Notifier->sent(): ' . print_r($properties, true));
    return true;
  }
}
?>