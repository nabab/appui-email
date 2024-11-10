<?php
/**
       * What is my purpose?
       *
       **/

/** @var bbn\Mvc\Model $model */
if ($model->hasData('id', true) && $model->hasData('folderId', true)) {
  return [
    'success' =>  $model->db->update('bbn_users_emails', [
      'id_folder' => $model->data['folderId']
    ], [
      'id' => $model->data['id']
    ]),
  ];
}
return [
  'success' => false,
];