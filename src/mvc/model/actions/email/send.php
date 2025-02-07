<?php
/** @var bbn\Mvc\Model $model */
use bbn\X;
$em = new bbn\User\Email($model->db);

//X::ddump($model->data, $em->getMailbox($model->data['id_account']));
$model->data['email']['from'] = $em->getAccount($model->data['id_account'])['login'];
return [
  'data' => $model->data['email'],
  'success' => $em->send($model->data['id_account'], $model->data['email'])
];