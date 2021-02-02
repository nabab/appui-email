<?php
/*
 * Describe what it does!
 *
 **/
use bbn\X;

$em = new bbn\User\Emails($model->db);
if ($model->hasData('limit')) {
  if (!$model->hasData('id_folder', true)) {
    $model->data['id_folder'] = 'inbox';
  }
  return $em->getList($model->data['id_folder'], $model->data);
}
else {
  return [
    'accounts' => $em->getAccounts(),
    'types' => bbn\User\Emails::getAccountTypes(),
    'contacts' => $em->getContacts(),
    'folder_types' => bbn\User\Emails::getFolderTypes()
  ];
}