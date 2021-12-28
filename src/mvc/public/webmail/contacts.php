<?php

/** @var $ctrl \bbn\Mvc\Controller */

if (empty($ctrl->post)) {
	$ctrl->combo(_('My contacts'));
}
else {
  $ctrl->action();
}