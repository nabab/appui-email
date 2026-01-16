<?php
/** @var bbn\Mvc\Model $model */

if ($model->hasData('action', true)) {
  $idSignatures = $model->inc->options->fromCode('signatures', 'email', 'appui');
  switch ($model->data['action']) {
    case 'delete':
      if ($model->hasData('id', true)) {
        return [
          'success' => (bool)$model->inc->pref->delete($model->data['id'])
        ];
      }

      break;
    case 'update':
      if ($model->hasData(['id', 'signature', 'name'], true)
        && $model->inc->pref->update($model->data['id'], [
          'name' => $model->data['name'],
          'signature' => $model->data['signature']
        ])
      ) {
        return [
          'success' => true,
          'data' => $model->inc->pref->get($model->data['id'])
        ];
      }

      break;
    case 'create':
      if ($model->hasData(['signature', 'name'], true)
        && ($id = $model->inc->pref->addToGroup($idSignatures, [
          'name' => $model->data['name'],
          'signature' => $model->data['signature'],
          'locale' => true
        ]))
      ) {
        return [
          'data' => $model->inc->pref->get($id),
          'success' => true
        ];
      }

      break;
  }
}

return [
  'success' => false
];
