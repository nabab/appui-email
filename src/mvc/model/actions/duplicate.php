<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 18:00
 *
 * @var $model \bbn\Mvc\Model
 */
$res = ['succes' => false];
if ( !empty($model->data['id']) ){
  $res['count'] = $model->getModel(APPUI_EMAIL_ROOT.'data/count');
  $mailings = new \bbn\Appui\Mailing($model->db);
  if ( $res['id'] = $mailings->copy($model->data['id']) ){
    $res['success'] = true;
  }
}
return $res;
