<?php

/** @var $model \bbn\Mvc\Model*/
if ($model->hasData('id', true)) {
	$em = new bbn\User\Emails($model->db);
  return $em->getEmail($model->data['id']);
}