<?php
use bbn\X;
use bbn\Str;
use bbn\User\Email;

$em = new Email($model->db);
$accounts = [];
$emAccounts = $em->getAccounts();
for ($i = 0; $i < count($emAccounts); $i++) {
  array_push($accounts, [
    "text" => $emAccounts[$i]['login'],
    "value" => $emAccounts[$i]['id']
  ]);
}

$signatures = [];
if ($idSignatures = $model->inc->options->fromCode('signatures', 'email', 'appui')) {
  $signatures = $model->inc->pref->getAll($idSignatures, true) ?: [];
}

$res = [
  'signatures' => $signatures,
  'success' => true,
  'isReply' => false,
  'email' => new stdClass(),
  'subject' => "",
  'to' => "",
  'accounts' => $accounts,
  'attachment' => []
];

if ($model->hasData('id', true)) {
  function createEmailListString(array $array): string {
    $r = '';
    foreach ($array as $a) {
      $r .= $a['mailbox']  . '@' . $a['host'] . ';';
    }

    return Str::sub($r, 0, -1);
  }

  $email =  $em->getEmail($model->data['id']);
  $email['login'] = $em->getLoginByEmailId($model->data['id'])['login'];
  $to = "";
  $subject = "";
  $originalMail = false;
  $attachment = [];
  if ($model->hasData('action', true)) {
    switch ($model->data['action']) {
      case 'edit':
        $res['to'] = createEmailListString($email['to']);
        $res['subject'] = $email['subject'];
        $res['attachment'] = $email['attachment'] ?: [];
        break;
      case 'reply':
        $res['to'] = createEmailListString($email['from']);
        $res['subject'] = quoted_printable_decode('RE : ' . $email['subject']);
        $res['isReply'] = true;
        $res['reply_to'] = $email['msg_unique_id'];
        $originalMail = true;
        break;
      case 'reply_all':
        $res['to'] = createEmailListString(X::mergeArrays($email['from'], $email['to']));
        $res['subject'] = quoted_printable_decode('RE : ' . $email['subject']);
        $res['isReply'] = true;
        $res['reply_to'] = $email['msg_unique_id'];
        $originalMail = true;
        break;
      case 'forward':
        $res['subject'] = quoted_printable_decode('TR : ' . $email['subject']);
        $res['attachment'] = $email['attachment'] ?: [];
        $originalMail = true;
        break;
      default:
        return [
          'success' => false,
          'error' => _('Unknown action')
        ];
    }

    if ($originalMail) {
      $header =  _('From : ') . createEmailListString($email['from']) . PHP_EOL
        . _('Send : ') . $email['Date'] . PHP_EOL
        . _('To : ') . createEmailListString($email['to']) . PHP_EOL
        . _('Subject : ') . $email['Subject'] . PHP_EOL;
      $email['plain'] = PHP_EOL. PHP_EOL . $header . $email['plain'];
      $email['html'] = '<br><br><div class="__bbn__signature"></div><br><hr><blockquote type="cite">' . nl2br($header) . ($email['html'] ?: nl2br($email['plain'])) . '</blockquote>';
    }
  }

  $res['email'] = $email;
  $res['references'] = $email['references'];
}
else {
  $res['email'] = [
    'html' => '<br><br><div class="__bbn__signature"></div>'
  ];
}

return $res;
