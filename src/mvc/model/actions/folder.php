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
      break;
    case 'create':
      if ($model->hasData('name')) {
        return [
          'type' => $types = $em->getFolderTypes(),
          'success' => $em->createFolder($model->data['id_account'], $model->data['name'], $model->data['id_parent'] ?? null),
					'folder' => $mb->listAllFolders(),
          'test' => $em->syncFolders($model->data['id_account']),
          'fields' => X::getField($types, ['code' => 'folders'], 'id')
        ];
      }
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