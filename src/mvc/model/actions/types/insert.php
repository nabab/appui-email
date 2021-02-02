<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 19:04
 *
 * @var $model \bbn\Mvc\Model
 */

if ( !empty($model->data['id_type']) &&
  !empty($model->data['title']) &&
  !empty($model->data['content']) &&
  !empty($model->data['name']) &&
  \bbn\Str::isUid($model->data['id_type'])
){
  $masks = new \bbn\Appui\Masks($model->db);
  if ( $mask = $masks->insert($model->data['name'], $model->data['id_type'], $model->data['title'], $model->data['content']) ){
    return [
      'success' => true,
      'data' => $masks->get($mask)
    ];
  }
}