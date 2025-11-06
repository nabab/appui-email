<?php
/** @var bbn\Mvc\Controller $ctrl */
if ($ctrl->hasArguments()
  && ($m = $ctrl->getModel([
    'id' => $ctrl->arguments[0]
  ]))
) {
  $ctrl->combo($m['subject'], $m);
}
