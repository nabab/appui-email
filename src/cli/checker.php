<?php
/**
 *  Describe what it does
 *
 **/
$user = false;

$mgr = $ctrl->inc->user->getManager();

$toCheck = [];

foreach($mgr->getUsers() as $userId) {
  if ($mgr->isOnline($userId)) {
    $toCheck[] = $userId;
  }
}

foreach($toCheck as $idUser) {
  $user = new bbn\User\Fake($idUser, $ctrl->inc->user);
  var_dump($user->getEmail());
}


//$id_accounts = $ctrl->inc->options->fromCode('accounts', 'email', 'appui');

//$accounts = $ctrl->inc->pref->getAll($id_accounts);