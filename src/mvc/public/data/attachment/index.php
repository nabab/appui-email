<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if ($ctrl->hasArguments()) {
  $file_path = $ctrl->arguments[1] . '/' . $ctrl->arguments[2] . '/' . $ctrl->arguments[3];
  $path = $ctrl->inc->user->getPath() . 'tmp_mail/' . $file_path;
  $ctrl->setMode("file");
  $ctrl->obj->file = $path;
}
else {
  $ctrl->action();
}

