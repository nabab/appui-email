<?php
use bbn\User\Email;

/** @var bbn\Mvc\Model $model */
if ($model->hasData('id', true)) {
  $em = new Email($model->db);
  if ($folderId = $em->getEmailFolderId($model->data['id'])) {
    $d = $em->getListAsThreads($folderId, [
      'filters' => [
        'conditions' => [[
          'field' => 'bbn_users_emails.id',
          'value' => $model->data['id']
        ]]
      ],
      'limit' => 1
    ]);
    if (!empty($d['data'])) {
      return $d['data'][0];
    }
  }
}