<?php
/** @var \bbn\Mvc\Controller $ctrl */
if (!empty($ctrl->arguments[0]) && ($ctrl->arguments[0] === 'num')) {
  $ctrl->addData(['num' => true]);
}
$ctrl->action();