<?php
if (!empty($ctrl->post['id'])
  && !empty($ctrl->post['uid'])
  && !empty($ctrl->post['mailbox'])
  && !empty($ctrl->post['mail'])
) {
  $ctrl->obj->success = true;
  $ctrl->obj->id = $ctrl->post['id'];
  $ctrl->obj->entities = $ctrl->getPluginModel('data/entities', $ctrl->post) ?: [];
}
else if (!empty($ctrl->post['id'])
  && !empty($ctrl->post['mailbox'])
  && !empty($ctrl->post['idEntity'])
) {
  $ctrl->obj->id = $ctrl->post['id'];
  $m = $ctrl->getPluginModel('data/entities', $ctrl->post) ?: [];
  $ctrl->obj->success = !empty($m['success']);
}
else {
  $ctrl->obj->success = false;
  $ctrl->obj->entities = [];
}