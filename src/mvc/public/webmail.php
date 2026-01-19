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
    'reader' => [
      'toolbar' => []
    ],
  ];
  foreach ($routes as $r) {
    if ($elements = $ctrl->getSubpluginModelGroup('webmail', $r['name'], 'appui-email')) {
      foreach ($elements as $name => $obj) {
        $n = explode('/', $name);
        $n = end($n);
        if (isset($slots[$n])) {
          foreach ($obj as $slot => $data) {
            if (isset($slots[$n][$slot])) {
              array_push($slots[$n][$slot], ...(X::isAssoc($data) ? [$data] : $data));
            }
          }
        }
      }
    }
  }

  foreach ($slots as $name => $slot) {
    foreach ($slot as $n => $s) {
      foreach ($s as $k => $v) {
        if (!isset($slots[$name][$n][$k]['priority'])) {
          $slots[$name][$n][$k]['priority'] = 5;
        }
      }

      X::sortBy($slots[$name][$n], 'priority');
    }
  }

  $ctrl->addData([
    'root' => APPUI_EMAIL_ROOT,
    'slots' => $slots
  ])
    ->setUrl(APPUI_EMAIL_ROOT . "webmail")
    ->combo(_('Webmail'), true);
}