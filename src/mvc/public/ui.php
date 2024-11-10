<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

$ctrl->addData(['root' => APPUI_EMAIL_ROOT])->setUrl(APPUI_EMAIL_ROOT . "ui")
    ->combo(_('UI'), true);