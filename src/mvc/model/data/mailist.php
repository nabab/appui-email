<?php

/** @var bbn\Mvc\Model $model */
if (\bbn\X::hasProp($model->data, 'recipients')) {
  $mailings = new \bbn\Appui\Mailing($model->db);
  $model->data['res'] = $model->getPluginModel('data/mailist', $model->data, 'emails');
  if ($model->data['res']['success'] && !empty($model->data['num'])) {
    $model->data['res']['num'] = count($model->data['res']['data']);
    unset($model->data['res']['data']);
  }
}

return $model->data['res'];
