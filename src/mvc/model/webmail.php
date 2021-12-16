<?php
use bbn\X;

$em = new bbn\User\Email($model->db);
if ($model->hasData('limit')) {
  if (!$model->hasData('id_folder', true)) {
    $model->data['id_folder'] = 'inbox';
  }

  return $em->getList($model->data['id_folder'], $model->data);
}
else {
  return [
    'root' => $model->data['root'],
    'accounts' => $em->getAccounts(),
    'types' => bbn\User\Email::getAccountTypes(),
    'contacts' => $em->getContacts(),
    'folder_types' => bbn\User\Email::getFolderTypes()
  ];
}
