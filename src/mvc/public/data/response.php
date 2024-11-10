<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if($ctrl->post['data']['external_uids']) {
  $ctrl->addData(['ids' => json_decode($ctrl->post['data']['external_uids'])->references]);
  $ctrl->action();
}