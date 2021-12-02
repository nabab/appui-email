<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
if ($ctrl->hasArguments()) {
  if ($model = $ctrl->getModel(['id' => $ctrl->arguments[0]])) {
    $doc = new DOMDocument();
    $dom = $doc->loadHTML($model['html']);
    //die(var_dump($doc));
    echo $model['html'];
  }
}