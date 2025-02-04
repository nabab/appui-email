<?php
$model->setStream();
return [];

$idAccount = $model->hasData('id_account', true) ? $model->data['id_account'] : null;
$idFolder = $model->hasData('id_folder', true) ? $model->data['id_folder'] : null;

if (empty($idAccount)) {

}