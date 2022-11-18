<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if ($ctrl->hasArguments()) {
  $ctrl->addData([
    'action' => $ctrl->arguments[0]
  ])->action();
}