<?php
use bbn\User\Email;
if ($model->hasData(['action'], true)) {
  $em = new Email($model->db);
  switch ($model->data['action']) {
    case 'insert':
    case 'update':
      if ($model->hasData(['name', 'host', 'login', 'pass'], true)
        && $model->hasData(['encryption', 'port'])
      ) {
        $data = [
          'name' => $model->data['name'],
          'host' => $model->data['host'],
          'encryption' => $model->data['encryption'],
          'port' => $model->data['port'],
          'login' => $model->data['login'],
          'pass' => $model->data['pass'],
          $em->getLocaleField() => $model->data[$em->getLocaleField()] ?? false
        ];
        try {
          if ($model->data['action'] === 'insert') {
            if ($idSmtp = $em->addSmtp($data)) {
              return [
                'success' => true,
                'data' => $em->getSmtp($idSmtp)
              ];
            }
          }
          elseif ($model->hasData('id', true)) {
            return [
              'success' => $em->updateSmtp($model->data['id'], $data)
            ];
          }
        }
        catch (\Exception $e) {
          return [
            'success' => false,
            'error' => $e->getMessage()
          ];
        }
      }

      break;

    case 'delete':
      if ($model->hasData('id', true)) {
        return [
          'success' => $em->deleteSmtp($model->data['id'])
        ];
      }
      break;
  }
}

return [
  'success' => false
];
