<?php
use bbn\User\Email;

$em = new Email($model->db);
if ($model->hasData('limit')) {
  if ($model->hasData('id_folder', true)) {
    $list = $model->hasData('threads', true) ?
      $em->getListAsThreads($model->data['id_folder'], $model->data) :
      $em->getList($model->data['id_folder'], $model->data);
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
    'smtps' => $em->getSmtps(),
    'types' => bbn\User\Email::getAccountTypes(),
    'contacts' => $em->getContacts(),
    'folder_types' => bbn\User\Email::getFolderTypes(),
    'hash' => $em->getHashes(),
  ];
}
