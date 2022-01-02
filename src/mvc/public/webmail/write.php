<?php

/** @var $ctrl \bbn\Mvc\Controller */
use bbn\X;
if ($ctrl->hasArguments()) {
  $ctrl->addData(['action' => $ctrl->arguments[0], 'id' => $ctrl->arguments[1]])->combo('$subject', true);
} else {
  $ctrl->combo('New email', true);
}