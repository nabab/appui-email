<?php
use bbn\User\Email;

if ($model->hasData('action', true)) {
  $em = new Email($model->db);
  switch ($model->data['action']) {
    case 'move':
      if ($model->hasData(['id', 'id_folder'], true)) {
        return [
          'success' => $em->moveEmailToFolder(
            $model->data['id'],
            $model->data['id_folder']
          )
        ];
      }
      break;
  }
}

return [
  'success' => false,
];