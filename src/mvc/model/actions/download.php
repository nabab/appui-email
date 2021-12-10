<?php

/** @var $model \bbn\Mvc\Model*/
$file = false;
if (\bbn\X::hasProps($model->data, ['id_media', 'id'])) {
  $mailing = new \bbn\Appui\Mailing($model->db);
  if ($medias = $mailing->getMedias($model->data['id'])) {
    $idx = \bbn\X::find($medias, ['id' => $model->data['id_media']]);
    if ($medias[$idx]) {
      $file = $medias[$idx]['file'];
    }
  }
}
return ['file' => $file];