<?php
use bbn\X;

$em = new bbn\User\Email($model->db);
if ($model->hasData('limit')) {
  if ($model->hasData('id_folder', true)) {
    $list = $em->getList($model->data['id_folder'], $model->data);
    if (is_null($list)) {
      return [
        'data' => [],
        'total' => 0
      ];
    }

    return $list;
  }

  return [
    'data' => [],
    'total' => 0
  ];
}
else {
  return [
    'root' => $model->data['root'],
    'accounts' => $em->getAccounts(),
    'types' => bbn\User\Email::getAccountTypes(),
    'contacts' => $em->getContacts(),
    'folder_types' => bbn\User\Email::getFolderTypes(),
    'hash' => $em->getHashes(),
  ];
}
