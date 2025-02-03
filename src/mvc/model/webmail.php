<?php
use bbn\X;

$em = new bbn\User\Email($model->db);
if ($model->hasData('limit')) {
  if (!$model->hasData('id_folder', true)) {
    $model->data['id_folder'] = 'inbox';
  }

  $list = $em->getList($model->data['id_folder'], $model->data);
  if (is_null($list)) {
    return null;
  }

  for ($i = 0; $i < count($list['data']); $i++) {
    $info = $em->getEmail($list['data'][$i]['id']);
    $list['data'][$i]['from'] = $info['fromaddress'];
    $list['data'][$i]['date'] = $info['date'];
    $list['data'][$i]['to'] = $info['toaddress'];
	}

  return $list;
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
