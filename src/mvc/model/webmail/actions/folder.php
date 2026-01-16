<?php
use bbn\X;
use bbn\Str;
use bbn\User\Email;
/** @var bbn\Mvc\Model $model */

if ($model->hasData(['action', 'id_account'], true)) {
  $em = new Email($model->db);
  switch($model->data['action']) {
    case 'delete':
      if ($model->hasData('id', true)) {
        return [
          'success' => $em->deleteFolder($model->data['id'], $model->data['id_account']),
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
        'error' => _("Folder's name not given")
      ];

    case 'rename':
      if ($model->hasData(['id', 'name'], true)) {
        return [
          'success' => $em->renameFolder($model->data['id'], $model->data['name']),
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }

      break;

    case 'move':
      if ($model->hasData(['to', 'folders'], true)) {
        $folders = $model->data['folders'];
        $err = [];
        $f = array_shift($folders);
        if ($success = $em->renameFolder($f['id'], $f['text'], $model->data['to'])) {
          foreach ($folders as $f) {
            if (!$em->renameFolderDb($f['id'],  $f['text'], $f['id_parent'])) {
              $err[] = $f;
            }
          }
        }
        return [
          'success' => $success && empty($err),
          'failed' => $err,
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }

      break;

    default:
      return [
        'success' => false,
        'error' => _('Action given not exist')
      ];
  }
}

return [
  'success' => false,
  'error' => _('No action given in the data request')
];