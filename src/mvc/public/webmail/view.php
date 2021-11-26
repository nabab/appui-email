<?php

/** @var $ctrl \bbn\Mvc\Controller */
$ctrl->combo("test", [
  'id' => $ctrl->arguments[0],
	'root' => APPUI_EMAIL_ROOT
]);