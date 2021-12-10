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
  $email['html'] =  $header . $purifier->purify(quoted_printable_decode($email['html']));
  return $email;
}