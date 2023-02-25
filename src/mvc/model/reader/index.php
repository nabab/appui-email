<?php
use bbn\X;
use bbn\User\Email;

/** @var $model \bbn\Mvc\Model*/
if ($model->hasData('id', true)) {
  $em = new Email($model->db);
  $email =  $em->getEmail($model->data['id']);
  $em->updateRead($model->data['id']);
  X::log($email);
  return $email;
}