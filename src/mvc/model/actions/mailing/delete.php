<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 18:00
 *
 * @var $model \bbn\Mvc\Model
 */

$success = false;
if ( !empty($model->data['id']) && !empty($model->data['state']) && (($model->data['state'] === 'ready') || ($model->data['state'] === 'cancelled')) ){
  
  $mailings = new \bbn\Appui\Mailing($model->db);
  
  $success = $mailings->delete($model->data['id']);
  return [
    'success' => $success,
    'count' => $model->getModel(APPUI_EMAIL_ROOT.'data/count')
  ];
}