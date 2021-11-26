<?php
/*
 * Describe what it does!
 *
 **/

/** @var $model \bbn\Mvc\Model*/
if ($model->hasData('id', true)) {
	$em = new bbn\User\Email($model->db);
  return $em->getEmail($model->data['id']);
}