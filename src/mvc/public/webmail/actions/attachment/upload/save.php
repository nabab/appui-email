<?php
use bbn\Str;
use bbn\File\Dir;

/** @var bbn\Mvc\Controller $ctrl */

if (!empty($ctrl->files['file'])
  && !empty($ctrl->post['timestamp'])
) {
  $f =& $ctrl->files['file'];
  $path = $ctrl->userTmpPath() . $ctrl->post['timestamp'];
  $name = $_REQUEST['name'] ?? $f['name'];
  $new = Str::encodeFilename($name, Str::fileExt($name));
  $final = $path . '/' . $new;

  if (Dir::createPath($path)
    && rename($f['tmp_name'], $final)
  ) {
    $ctrl->obj->success = true;
    $ctrl->obj->fichier = [
      'name' => $new,
      'original' => $name,
      'size' => filesize($final),
      'extension' => '.' . Str::fileExt($new),
      'path' => $final
    ];
  }
}