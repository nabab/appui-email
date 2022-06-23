<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
use bbn\X;
if ($ctrl->hasArguments()) {
  $ctrl->addData(['id' => $ctrl->arguments[0]])->combo();
}