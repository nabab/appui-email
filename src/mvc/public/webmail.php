<?php
if (isset($ctrl->post['limit'])) {
  if (!empty($ctrl->post['data'])) {
    if (isset($ctrl->post['data']['id_folder'])) {
      $ctrl->addData(['id_folder' => $ctrl->post['data']['id_folder']]);
    }

    if (isset($ctrl->post['data']['threads'])) {
      $ctrl->addData(['threads' => $ctrl->post['data']['threads']]);
    }
  }

  $ctrl->action();
}
else {
  $ctrl->addData(['root' => APPUI_EMAIL_ROOT])
    ->setUrl(APPUI_EMAIL_ROOT . "webmail")
    ->combo(_('Webmail'), true);
}