<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 15/06/2018
 * Time: 17:32
 *
 * @var $model \bbn\Mvc\Model
 */

if (
  !empty($model->data['id']) 
  && !empty($model->data['users'])
  && ($mailings = new \bbn\Appui\Mailings($model->db))
  && ($mail = $mailings->getMailing($model->data['id']))
){
 
  $num = 0;
  $cfg = [
    'subject' => $mail['title'],
    'text' => $mail['content'],
  ];
  if ( !empty($mail['medias']) ){
    $cfg['attachments'] = [];
    foreach ( $mail['medias'] as $media ){
      $cfg['attachments'][$media['name']] = $media['file'];
    }
  }
  if (is_string($model->data['users'])) {
    $model->data['users'] = [$model->data['users']];
  }
  foreach ($model->data['users'] as $u) {
    if ($cfg['to'] = $model->db->selectOne('bbn_users', 'email', ['id' => $u])) {
      $num += (int)$mailings->send($cfg, $mail['sender']);
    }
  }
  return ['success' => true, 'num' => $num];
}
return ['success' => false];