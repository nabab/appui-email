<?php
use bbn\User\Email;
if ($model->hasData(['action'], true)) {
  $em = new Email($model->db);
  switch ($model->data['action']) {
    case 'insert':
    case 'update':
      if ($model->hasData(['name', 'host', 'login', 'pass', 'encryption'], true)
        && $model->hasData(['port', 'validatecert'])
      ) {
        $data = [
          'name' => $model->data['name'],
          'host' => $model->data['host'],
          'login' => $model->data['login'],
          'pass' => $model->data['pass'],
          'encryption' => $model->data['encryption'],
          'port' => $model->data['port'],
          'validatecert' => !empty($model->data['validatecert']) ? 1 : 0,
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
          elseif ($model->hasData('id', true)
            && $em->updateSmtp($model->data['id'], $data)
          ) {
            return [
              'success' => true,
              'data' => $em->getSmtp($model->data['id'])
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
