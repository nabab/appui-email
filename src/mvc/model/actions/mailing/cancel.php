<?php


$success = 0;
//case of cancelling a single row
if ( !empty($model->data['id']) && !empty($model->data['state']) && ( $model->data['state'] === 'sending') ){
  $mailings = new \bbn\Appui\Mailing($model->db);
  //changes the state only to the emails not yet sent ('ready')
  $emails = $model->db->rselectAll('bbn_emails', [], ['id_mailing' => $model->data['id'], 'status' => 'ready']);
  
  if ( !empty($emails) ){
    foreach ( $emails as $e ){
      $model->db->update('bbn_emails', ['status' => 'cancelled'], [
        'id' => $e['id'],
      ]);
    }
  }
  $success = $mailings->changeState($model->data['id'], 'cancelled');
}
//note yet included in js functions
//cancelling multiple rows
else if ( !empty($model->data['selected']) ){
  
  foreach ( $model->data['selected'] as $s ){
    if ($model->db->update('bbn_emails', [ 'state' => 'cancelled'], [
      'id' => $s['id'],
      'email' => $s['email']
    ])){
      $success ++;
    }
  }
}
return ['success' => $success ];