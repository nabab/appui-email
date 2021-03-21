<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 06/04/2018
 * Time: 12:25
 *
 * @var $model \bbn\Mvc\Model
 */

if ( !empty($model->data['id']) &&
  $model->db->update('bbn_emailings', ['sent' => date('Y-m-d H:i:s')], ['id' => $model->data['id']])
){
  return [
    'success' => true,
    'count' => $model->getModel(APPUI_EMAIL_ROOT.'data/count')
  ];
}