<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
if ($ctrl->hasArguments()) {
  if ($model = $ctrl->getModel(['id' => $ctrl->arguments[0]])) {
    echo $ctrl->getView($model);
  }
}