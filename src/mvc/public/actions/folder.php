<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if ($ctrl->hasArguments()) {
  $ctrl->addData([
    'action' => $ctrl->arguments[0]
  ])->action();
}