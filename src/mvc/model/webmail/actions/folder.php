<?php
/**
       * What is my purpose?
       *
       **/

use bbn\X;
use bbn\Str;
use bbn\User\Email;
/** @var bbn\Mvc\Model $model */

if ($model->hasData(['action', 'id_account'], true)) {
  $em = new Email($model->db);
  switch($model->data['action']) {
    case 'delete':
      if ($model->hasData('id')) {
        $success = true;
        $res = [];
        foreach ($model->data['id'] as $folder) {
          $tmp =  $em->deleteFolder($folder['id'], $model->data['id_account']);
          if ($success && !$tmp) {
            $success = false;
          }
          array_push($res, [
            'success' => $tmp,
            'text' => $folder['text']
          ]);
        }
        return [
          'success' => $success,
          'res' => $res,
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
      break;
    case 'create':
      if ($model->hasData('name', true)) {
        return [
          'success' => $em->createFolder(
            $model->data['id_account'],
            $model->data['name'],
            !empty($model->data['id_parent']) ? $model->data['id_parent'] : null
          ),
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
      return [
        'success' => false,
        'error' => 'Folder\'s name not given'
      ];
      break;
    case 'rename':
      if ($model->hasData(['name', 'folders'])) {
        $folders = $model->data['folders'];
        $success = true;
        $res = [];
        $success = $em->renameFolder($folders[0]['id'], $model->data['name'], $model->data['id_account'], $folders[0]['id_parent']);
        for ($i = 1; $i < count($folders); $i++){
          $tmp = $em->renameFolderDb($folders[$i]['id'],  $folders[$i]['text'], $model->data['id_account'], $folders[$i]['id_parent']);
          if ($success && !$tmp) {
            $success = false;
          }
          array_push($res, [
            'success' => $tmp,
            'text' => $folders[$i]['text']
          ]);
        }
        return [
          'success' => $success,
          'res' => $res,
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
    case 'move':
      if ($model->hasData(['to', 'folders'])) {
        $to = $model->data['to'];
        $folders = $model->data['folders'];
        $success = true;
        $res = [];
        $success = $em->renameFolder($folders[0]['id'], $folders[0]['text'], $model->data['id_account'], $to['id'] ?? null);
        for ($i = 1; $i < count($folders); $i++){
          $tmp = $em->renameFolderDb($folders[$i]['id'],  $folders[$i]['text'], $model->data['id_account'], $folders[$i]['id_parent']);
          if ($success && !$tmp) {
            $success = false;
          }
          array_push($res, [
            'success' => $tmp,
            'text' => $folders[$i]['text']
          ]);
        }
        return [
          'success' => $success,
          'res' => $res,
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
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