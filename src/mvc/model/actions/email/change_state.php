<?php
$success = 0;
//case of cancelling a single row
if ( !empty($model->data['id']) && !empty($model->data['status']) && ($model->data['status'] === 'cancelled') ){
  $mailings = new \bbn\Appui\Mailing($model->db);
  $success = $mailings->changeEmailStatus($model->data['id'], 'ready'); 
  return ['success' => $success];
}