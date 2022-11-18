<?php

/** @var $ctrl \bbn\Mvc\Controller */
use bbn\X;
if ($ctrl->hasArguments()) {
  if ($ctrl->arguments[0] == 'new') {
    $ctrl->combo('New #' . $ctrl->arguments[1], true);
  } else {
    $ctrl->addData(['action' => $ctrl->arguments[0], 'id' => $ctrl->arguments[1]])->combo('$subject', true);
  }
}