<?php

if ( !empty($model->data['id']) && ( $state = $model->data['state']) ){
  $mailings = new \bbn\Appui\Mailing($model->db);
  $success = $mailings->changeState($model->data['id'], $state);
  return [
    'success' => $success,
		'count' => $model->getModel(APPUI_EMAIL_ROOT.'data/count'),
  ];
}