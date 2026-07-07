<?php
use bbn\X;
use bbn\Str;
use bbn\User\Email;
/** @var bbn\Mvc\Model $model */

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
      if (!empty($a['email'])) {
        $r .= $a['email'] . ';';
      }
      else if (!empty($a['mailbox']) && !empty($a['host'])) {
        $r .= $a['mailbox']  . '@' . $a['host'] . ';';
      }
    }

    return Str::sub($r, 0, -1);
  }

  $email =  $em->getEmail($model->data['id']);
  $email['login'] = $em->getLoginByEmailId($model->data['id'])['login'];
  $to = "";
  $subject = "";
  $originalMail = false;
  $entities = false;
  $attachment = [];
  if ($model->hasData('action', true)) {
    switch ($model->data['action']) {
      case 'edit':
        $res['to'] = createEmailListString($email['to'] ?: []);
        $res['subject'] = $email['subject'];
        $res['attachment'] = $email['attachment'] ?: [];
        break;
      case 'reply':
        $res['to'] = createEmailListString($email['from'] ?: []);
        $res['subject'] = quoted_printable_decode('RE : ' . $email['subject']);
        $res['isReply'] = true;
        $res['reply_to'] = $email['msg_unique_id'];
        $originalMail = true;
        $entities = true;
        break;
      case 'reply_all':
        $res['to'] = createEmailListString(X::mergeArrays($email['from'] ?: [], $email['to'] ?: []));
        $res['subject'] = quoted_printable_decode('RE : ' . $email['subject']);
        $res['isReply'] = true;
        $res['reply_to'] = $email['msg_unique_id'];
        $originalMail = true;
        $entities = true;
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
      $header =  _('From : ') . createEmailListString($email['from'] ?: []) . PHP_EOL
        . _('Send : ') . $email['date'] . PHP_EOL
        . _('To : ') . createEmailListString($email['to'] ?: []) . PHP_EOL
        . _('Subject : ') . $email['subject'] . PHP_EOL;
      $email['plain'] = PHP_EOL. PHP_EOL . $header . $email['plain'];
      $email['html'] = '<br><br><div class="__bbn__signature"></div><br><hr><blockquote type="cite">' . nl2br($header) . ($email['html'] ?: nl2br($email['plain'])) . '</blockquote>';
    }

    if (!empty($entities)
      && $model->hasData('id', true)
      && !empty($email['from'])
      && !empty($email['from'][0]['email'])
      && !empty($email['msg_unique_id'])
      && !empty($email['id_account'])
      && ($m = $model->getPluginModel('data/entities', [
        'id' => $model->data['id'],
        'uid' => $email['msg_unique_id'],
        'mailbox' => $email['id_account'],
        'mail' => $email['from'][0]['email']
      ]))
    ) {
      $res['entities'] = $m['entities'] ?? [];
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
