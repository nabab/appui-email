<?php

/** @var $ctrl \bbn\Mvc\Controller */

if (isset($ctrl->post['data']['id']) && isset($ctrl->post['data']['folderId'])) {
  $ctrl->action()
}

return [
  'success' => false
];