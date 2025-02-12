<?php
if (!empty($ctrl->post['id'])
  && !empty($ctrl->post['uid'])
  && !empty($ctrl->post['mailbox'])
  && !empty($ctrl->post['from'])
) {
  $ctrl->obj->success = true;
  $ctrl->obj->data = [
    'id' => $ctrl->post['id'],
    'entities' => $ctrl->getPluginModel('data/entities', $ctrl->post) ?: []
  ];
}
else {
  $ctrl->obj->success = false;
  $ctrl->obj->data = [];
}