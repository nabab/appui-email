<?php
use bbn\X;
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
  $routes = $ctrl->getRoutes();
  $slots = [
    'toolbar' => []
  ];
  foreach ($routes as $r) {
    if ($elements = $ctrl->getSubpluginModelGroup('webmail', $r['name'], 'appui-email')) {
      foreach ($elements as $obj) {
        foreach ($obj as $slot => $data) {
          if (isset($slots[$slot])) {
            array_push($slots[$slot], ...(X::isAssoc($data) ? [$data] : $data));
          }
        }
      }
    }
  }

  foreach ($slots as &$s) {
    foreach ($s as &$m) {
      if (!isset($m['priority'])) {
        $m['priority'] = 5;
      }
    }

    unset($m);
    X::sortBy($s, 'priority');
  }

  $ctrl->addData([
    'root' => APPUI_EMAIL_ROOT,
    'slots' => $slots
  ])
    ->setUrl(APPUI_EMAIL_ROOT . "webmail")
    ->combo(_('Webmail'), true);
}