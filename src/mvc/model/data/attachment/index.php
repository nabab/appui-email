<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/

$media = new bbn\Appui\Medias($model->db);



$em = new bbn\User\Email($model->db);

$email = $em->getEmail($model->data['id']);

$description = 'Attachment of ' . '"' . $email['subject'] . '" from ' . $email['senderaddress'] . ' at ' . $email['date'];

$id = null;

if ($model->data['mode'] == 'private_media') {
  $path = $model->inc->user->getPath() . 'tmp_mail/' . $model->data['path'];
	$filename = $model->data['filename'];
  $id = $media->insert($path, NULL, $filename, 'file', true, $description);
} else if ($model->data['mode'] == 'shared_media') {
  $path = $model->inc->user->getPath() . 'tmp_mail/' . $model->data['path'];
	$filename = $model->data['filename'];
  $id = $media->insert($path, NULL, $filename, 'file', false, $description);
} else if ($model->data['mode'] == 'shared_media_all') {
  
} else if ($model->data['mode'] == 'private_media_all') {
  
}


return [
  'success' => $id !== null
];