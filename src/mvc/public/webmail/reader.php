<?php
/** @var bbn\Mvc\Controller $ctrl */
if ($ctrl->hasArguments()) {
  if ($model = $ctrl->getModel(['id' => $ctrl->arguments[0]])) {
    echo $model['html'];
  }
}