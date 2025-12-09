<?php
/** @var bbn\Mvc\Controller $ctrl */
use bbn\X;
if ($ctrl->hasArguments()) {
  $ctrl->addData(['id' => $ctrl->arguments[0]])->combo();
}