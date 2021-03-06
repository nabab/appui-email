<?php
$success = false;
$mailings = new \bbn\Appui\Mailing($model->db);
 

/* a non admin user can delete only mail with status 'cancelled' or 'ready'.
   Admin users can delete also email with status 'success'
*/

$user = new \bbn\User($model->db);
if (!empty($model->data['id_user']) ){
  $is_admin = $user->isAdmin($model->data['id_user']);
}


if ( 
  !empty($model->data['id']) && 
  !empty($model->data['status']) && 
  ( 
    ($model->data['status'] === 'ready') ||
    ($model->data['status'] === 'cancelled') || 
    (($model->data['status'] === 'success') && $is_admin ) 
  ) 
){
  $success = $mailings->deleteEmail($model->data['id']); 
}
else if ( !empty($model->data['selected']) ){
  $selected = $model->data['selected']; 
  foreach ( $selected as $s ){
    $n = 0;
    if ( ($s['status'] === 'ready') || ($s['status'] === 'cancelled') ||
       ( ($s['status'] === 'success') && $is_admin ) 
    ){
      if ( $mailings->deleteEmail($s['id'])  ){
        $n++;
      }
    }
    $success = $n;
  }
}
return [
  'success' => $success
];