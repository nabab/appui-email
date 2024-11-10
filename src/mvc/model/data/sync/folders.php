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

$sync = [];

foreach ($accounts as $a) {
  $folders = $model->inc->pref->get($a['id']);
  $sync[$a['id']] = [
    'name' => $a['login'],
    'id' => $a['id'],
    'folders' => $folders['sync']['folders']
  ];
}

return [
  'sync' => $sync
];

