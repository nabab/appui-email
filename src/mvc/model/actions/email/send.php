<?php
/**
   * What is my purpose?
   *
   **/

/** @var $model \bbn\Mvc\Model*/
use bbn\X;

$em = new bbn\User\Email($model->db);

//X::ddump($model->data, $em->getMailbox($model->data['id_account']));
return [
  'success' => $em->send($model->data['id_account'], $model->data)
];