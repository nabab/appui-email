<?php
/**
       * What is my purpose?
       *
       **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/

$em = new bbn\User\Email($model->db);

$filters = [
  'logic' => 'OR',
  'conditions' => []
];

foreach($model->data['ids'] as $id) {
  $filters['conditions'][] = [
    'field' => 'msg_unique_id',
    'value' => $id
  ];
}

$model->data['filters'] = $filters;

return $em->getEmailByUID($model->data);