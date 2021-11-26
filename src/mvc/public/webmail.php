<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller
 *
 */
if (isset($ctrl->post['limit'])) {
  if (isset($ctrl->post['data'], $ctrl->post['data']['id_folder'])) {
	  $ctrl->addData(['id_folder' => $ctrl->post['data']['id_folder']]);
  }
  $ctrl->action();
}
else {
  $ctrl->addData(['root' => APPUI_EMAIL_ROOT])->setUrl(APPUI_EMAIL_ROOT . "webmail")
    ->combo(_('Webmail'), true);
}