<?php

/** @var bbn\Mvc\Controller $ctrl */
if (isset($ctrl->post['id_media'])) {
  $model = $ctrl->getModel($ctrl->post);
  if (!empty($model['file'])) {
    $ctrl->obj->file = $model['file'];
  }
}
if (!isset($ctrl->obj->file)) {
  $ctrl->obj->error = _("Impossible to find the requested file");
}