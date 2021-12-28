<?php
/**
 * What is my purpose?
 *
 **/

/** @var $model \bbn\Mvc\Model*/

$user = new bbn\User($model->db);
return [
  'success' => true,
  'path' => $user->addToTmp($model->data['file']['tmp_name'], $model->data['file']['name']),
];