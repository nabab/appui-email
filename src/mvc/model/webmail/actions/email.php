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
    case 'send':
      if ($model->hasData(['id_account', 'email'], true)
        && !empty($model->data['email']['to'])
        && ( !empty($model->data['email']['title'])
          || !empty($model->data['email']['text']))
        && ($account = $em->getAccount($model->data['id_account']))
      ) {
        $model->data['email']['from'] = $account['login'];
        return [
          'data' => $model->data['email'],
          'success' => $em->send(
            $model->data['id_account'],
            $model->data['email']
          )
        ];
      }

      break;
    case 'draft':
      if ($model->hasData(['id_account', 'email'], true)
        && ($account = $em->getAccount($model->data['id_account']))
      ) {
        $model->data['email']['from'] = $account['login'];
        try {
          return [
            'data' => $model->data['email'],
            'success' => $em->saveDraft(
              $model->data['id_account'],
              $model->data['email']
            )
          ];
        }
        catch (\Exception $e) {
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