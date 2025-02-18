<?php
use bbn\User\Email;
use bbn\File\Dir;
if (!empty($ctrl->post['id'])
  && !empty($ctrl->post['filename'])
) {
  $emailClass = new Email($ctrl->db);
  if ($file = $emailClass->getAttachments($ctrl->post['id'], $ctrl->post['filename'])
  ) {
    $path = $ctrl->userTmpPath(null, 'appui-email');
    if (Dir::createPath($path)
      && file_put_contents($path.$ctrl->post['filename'], base64_decode($file['data']))
    ) {
      $ctrl->setMode("file");
      $ctrl->obj->file = $path.$ctrl->post['filename'];
    }
  }
}
