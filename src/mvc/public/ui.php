<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

$ctrl->addData(['root' => APPUI_EMAIL_ROOT])->setUrl(APPUI_EMAIL_ROOT . "ui")
    ->combo(_('UI'), true);