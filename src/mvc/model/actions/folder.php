<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\User\Email;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData(['action', 'id_account'], true)) {
  $em = new Email($model->db);
  $mb = $em->getMailbox($model->data['id_account']);
  switch($model->data['action']) {
    case 'delete':
      if ($model->hasData('id')) {
        return [
          'success' => $em->deleteFolder($model->data['id'], $model->data['id_account'])
        ];
      }
      break;
    case 'create':
      if ($model->hasData('name')) {
        return [
          'success' => $em->createFolder($model->data['id_account'], $model->data['name'], $model->data['id_parent'] ?? null),
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
      return [
        'success' => false,
        'error' => 'Name of folder not given'
      ];
      break;
    default:
      return [
        'success' => false,
        'error' => 'Action given not exist'
      ];
  }
}

return [
  'success' => false,
  'error' => 'No action given in the data request'
];