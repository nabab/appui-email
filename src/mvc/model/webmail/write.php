<?php
/**
               * What is my purpose?
               *
               **/

/** @var $model \bbn\Mvc\Model*/
use bbn\X;

$id_signatures = $model->inc->options->fromCode('signatures', 'email', 'appui');

function createEmailListString(array $array): string {
  $res = '';
  for ($i = 0; $i < count($array); $i++) {
    $res .= $array[$i]->mailbox  . '@' . $array[$i]->host . ' ';
  }
  return substr($res, 0, -1);
}

$em = new bbn\User\Email($model->db);

$accounts = [];

$emAccounts = $em->getAccounts();

for ($i = 0; $i < count($emAccounts); $i++) {
  array_push($accounts, $emAccounts[$i]['login']);
}

if ($model->hasData('id', true)) {
  $email =  $em->getEmail($model->data['id']);
  $header =  _('From : ') . createEmailListString($email['from']) . PHP_EOL . _('Send : ') . $email['Date'] . PHP_EOL . _('To : ') . createEmailListString($email['to']) . PHP_EOL . _('Subject : ') . $email['Subject'] . PHP_EOL;
  $email['plain'] = PHP_EOL. PHP_EOL . $header . $email['plain'];
  if (!empty($email['html'])) {
    $email['html'] = nl2br(PHP_EOL . PHP_EOL . '<hr>' . $header) . $email['html'];
  } else {
    $email['html'] = nl2br($email['plain']);
  }
  $to = "";
  $subject = "";
  if ($model->hasData('action', true)) {
    if ($model->data['action'] == 'reply') {
      $to = createEmailListString($email['from']);
      $subject = 'RE : ' . $email['subject'];
    }
    if ($model->data['action'] == 'reply_all') {
      $to = createEmailListString($email['from']) . " " . createEmailListString($email['to']);
      $subject = 'RE : ' . $email['subject'];
    }
    if ($model->data['action'] == 'forward') {
      $subject = 'TR : ' . $email['subject'];
    }
  }

  $email['login'] = $em->getLoginByEmailId($model->data['id'])['login'];
  return [
    'signatures' => $model->inc->pref->getAll($id_signatures, true),
    'success' => true,
    'email' => $email,
    'subject' => $subject,
    'to' => $to,
    'accounts' => $accounts,
  ];
}

return [
  'signatures' => $model->inc->pref->getAll($id_signatures, true),
  'success' => true,
  'email' => [
    'email' => false,
  ],
  'subject' => "",
  'to' => "",
  'accounts' => $accounts,
];