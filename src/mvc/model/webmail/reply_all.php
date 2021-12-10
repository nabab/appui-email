<?php
/*
   * Describe what it does!
   *
   **/
use bbn\X;
/** @var $model \bbn\Mvc\Model*/

function createEmailListString(array $array): string {
        $res = '';
        for ($i = 0; $i < count($array); $i++) {
          $res .= $array[$i]->mailbox  . '@' . $array[$i]->host . ' ';
        }
        return $res;
}

if ($model->hasData('id', true)) {
  $em = new bbn\User\Email($model->db);
  $email =  $em->getEmail($model->data['id']);
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
  $header = '<br><br><hr>' . _('From : ') . createEmailListString($email['from']) . '<br>' . _('Send : ') . $email['Date'] . '<br>' . _('To : ') . createEmailListString($email['to']) . '<br>' . _('Subject : ') . $email['Subject'] . '<br><br>';
  $email['html'] =  $header . $purifier->purify(quoted_printable_decode($email['html']));
  $email['login'] = $em->getLoginByEmailId($model->data['id'])['login'];
  return $email;
}