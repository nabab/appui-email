<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
use bbn\X;


if ($ctrl->hasArguments()) {
  if ($model = $ctrl->getModel(['id' => $ctrl->arguments[0]])) {
		
    echo $model['html'];
  }
}