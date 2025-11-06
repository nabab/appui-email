<?php
/**
 * What is my purpose?
 *
 **/

/** @var bbn\Mvc\Model $model */

if ($model->hasData('id', true)) {
  $em = new bbn\User\Email($model->db);
  $acc = $em->getFolder($model->data['id'])['id_account'];
  return [
    'success' => true,
    'data' =>   $em->getFolders($acc),
    'account' => $acc,
  ];
}

return [
  'success' => false,
];