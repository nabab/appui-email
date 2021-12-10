<?php
use bbn\X;

/** @var $model \bbn\Mvc\Model*/
$em         = new bbn\User\Emails($model->db);
$pw         = new bbn\Appui\Passwords($model->db);
$deleted    = [];
$subscribed = [];
$added      = [];
/*
$added[]  = $em->addAccount(
  [
    'pass'  => $pw->userGet('74af26b82a8d11eba49b366237393031', $model->inc->user),
    'login' => 'thomas@babna.com',
    'host'  => 'server.babna.com',
    'type'  => 'imap'
  ]
);
$added[]  = $em->deleteAccount('07d079aa2ba011eba49b366237393031');
*/
// CLI
$done = [];
$accounts = $em->getAccounts();
foreach ($accounts as $a) {
  $deleted[] = $em->reset($a['id']);
  /*
  $em->syncFolders($a['id']);
  X::map(
    function ($folder) use (&$em, &$done) {
      if ($folder = $em->checkFolder($folder)) {
        $done[$folder['uid']] = $em->syncEmails($folder);
      }
    },
    $em->getFolders($a['id']),
    'items'
  );
  $subscribed[$a['login']] = $em->getFolders($a['id']);
  break;
  */
}

return [
  'done' => $done,
  //'types' => bbn\User\Emails::getFolderTypes(),
  //'folders' => bbn\User\Emails::getOptions('folders'),
  //'added' => $added,
  'subscribed' => $subscribed,
  //'deleted' => $deleted,
  //'data' => $accounts
];