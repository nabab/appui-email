<?php

/** @var $model \bbn\Mvc\Model*/
if (!$model->hasData('id_folder', true)) {
  $model->data['id_folder'] = 'inbox';
}

$em = new bbn\User\Emails($model->db);
return $em->getList($model->data['id_folder'], $model->data);