<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if ($ctrl->hasArguments()) {
  $file_path = $ctrl->arguments[0] . '/' . $ctrl->arguments[1] . '/' . $ctrl->arguments[2];
  $path = $ctrl->inc->user->getPath() . 'tmp_mail/';
  if (!empty($file_path) && is_file($path . $file_path))
  {
    $mimeType = mime_content_type($path . $file_path);
    // Check if the file is an image
    if (str_starts_with($mimeType, 'image/')) {
      $ctrl->setMode("image");
	    $ctrl->obj->img = $path . $file_path;
    } else {
      $ctrl->setMode("file");
      $ctrl->obj->file = $path . $file_path;
    }
  }
} else {
  return [
    "res" => [
      'test'
    ]
  ];
}
