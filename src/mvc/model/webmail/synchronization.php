<?php
/**
     * What is my purpose?
     *
     **/

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Model $model */

$em = new bbn\User\Email($model->db);

$sync = [];

$accounts = $em->getAccounts();

foreach ($accounts as $a) {
  $folders = $em->getFolders($a['id']);
  $sync[$a['id']] = [
    'name' => $a['login'],
    'id' => $a['id']
  ];

  foreach ($folders as $f) {
    $folder = $em->getFolder($f['id']);
    $info = $em->getInfoFolder($f['id']);
    $sync[$a['id']]['folders'][$f['id']] = [
      'id' => $f['id'],
      'name' => $folder['uid'],
      'db_msg' => $folder['num_msg'],
      'msg' => $info->Nmsgs,
    ];
  }
}

X::log($sync, "test_sync");

return [
  'sync' => $sync
];