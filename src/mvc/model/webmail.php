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

    foreach ($list['data'] as &$d) {
      $d['id_task'] = $model->db->selectOne(
        'bbn_tasks',
        'id',
        [
          ['cfg', 'contains', "%".$d['msg_unique_id']."%"],
          ['id_user', '=', $model->inc->user->getId()]
        ]
      );
      if ($model->hasData('threads', true)
        && !empty($d['thread'])
      ) {
        foreach ($d['thread'] as $i => &$t) {
          if (!$i) {
            $t['id_task'] = $d['id_task'];
            $t['is_task'] = $d['is_task'];
            continue;
          }

          $t['id_task'] = $model->db->selectOne(
            'bbn_tasks',
            'id',
            [
              ['cfg', 'contains', "%".$t['msg_unique_id']."%"],
              ['id_user', '=', $model->inc->user->getId()]
            ]
          );
        }

        unset($t);
      }
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
    'slots' => $model->data['slots']
  ];
}
