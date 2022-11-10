<?php
/**
               * What is my purpose?
               *
               **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/

$id_signatures = $model->inc->options->fromCode('signatures', 'email', 'appui');

switch($model->data['action']) {
  case 'get':
    return [
      'success' => true,
      'res' => $model->inc->pref->getAll($id_signatures, true)
    ];
  case 'delete':
    if ($model->hasData(['id'])) {
      return [
        'success' => $model->inc->pref->delete($model->data['id'])
      ];
    }
  case 'update':
    if ($model->hasData(['id', 'signature', 'name'])) {
      return [
        'success' => $model->inc->pref->update($model->data['id'], [
          'text' => "Signatures",
          'name' => $model->data['name'],
          'signature' => $model->data['signature']
        ]),
        'signature' => $model->inc->pref->get($model->data['id'])
      ];
    }
  case 'create':
    if ($model->hasData(['signature', 'name'])) {
      $id = $model->inc->pref->addToGroup($id_signatures, [
        'text' => 'Signatures',
        'name' => $model->data['name'],
        'signature' => $model->data['signature']
      ]);
      if (!$id) {
        return [
          'success' => false
        ];
      }
      return [
        'signature' => $model->inc->pref->get($id),
        'success' => true
      ];
    }
  default:
    return [
      'success' => false
    ];
}
