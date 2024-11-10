<?php

/** @var bbn\Mvc\Controller $ctrl */
$ctrl->combo("test", [
  'id' => $ctrl->arguments[0],
	'root' => APPUI_EMAIL_ROOT
]);