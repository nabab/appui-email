<?php
/** @var bbn\Mvc\Controller $ctrl */
use bbn\X;

$em         = new bbn\User\Email($ctrl->db);
$pw         = new bbn\Appui\Passwords($ctrl->db);
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
  X::adump($em->getFolders($a['id']));
  //$deleted[] = $em->deleteAccount($a['id']);
  $em->syncFolders($a['id']);
  X::map(
    function ($folder) use (&$em, &$done, &$a) {
      $check = $em->checkFolder($folder);
      if ($check) {
        $done[$a['host'].' - '.$folder['uid']] = $em->syncEmails($folder);
      }
      return $folder;
    },
    $em->getFolders($a['id']),
    'items'
  );
  $subscribed[$a['login']] = $em->getFolders($a['id']);
  //var_dump($em->getFolders($a['id']));
}

X::adump([
  'done' => $done,
  //'types' => bbn\User\Emails::getFolderTypes(),
  //'folders' => bbn\User\Emails::getOptions('folders'),
  //'added' => $added,
  'subscribed' => $subscribed,
  'deleted' => $deleted,
  //'data' => $accounts,
]);