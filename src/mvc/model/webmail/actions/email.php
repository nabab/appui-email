<?php
use bbn\User\Email;

if ($model->hasData('action', true)) {
  $em = new Email($model->db);
  switch ($model->data['action']) {
    case 'move':
      if ($model->hasData(['id', 'id_folder'], true)) {
        try {
          $success = $em->moveEmailToFolder($model->data['id'], $model->data['id_folder']);
          return [
            'success' => $success
          ];
        }
        catch (Exception $e) {
          return [
            'success' => false,
            'error' => $e->getMessage()
          ];

        }
      }
      break;
  }
}

return [
  'success' => false,
];