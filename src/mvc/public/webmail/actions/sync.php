<?php
use bbn\User\Email;
use bbn\X;

$ctrl->setStream();
$emailClass = new Email($ctrl->db);
$folders = [];

function addFolders($list, &$folders){
  foreach ($list as $l) {
    if (!empty($l['items'])) {
      $items = $l['items'];
      unset($l['items']);
      $folders[] = $l;
      addFolders($items, $folders);
      return;
    }

    $folders[] = $l;
  }
}

if (!empty($ctrl->post['id_folder'])) {
  if ($f = $emailClass->getFolder($ctrl->post['id_folder'])) {
    $folders[] = $f;
  }
}
else if (!empty($ctrl->post['id_account'])) {
  if ($accountFolders = $emailClass->getFolders($ctrl->post['id_account'])) {
    addFolders(array_values($accountFolders), $folders);
  }
}
else if ($accounts = $emailClass->getAccountsIds()) {
  foreach ($accounts as $a) {
    if ($accountFolders = $emailClass->getFolders($a)) {
      addFolders(array_values($accountFolders), $folders);
    }
  }
}

if (!empty($folders)) {
  $total = 0;
  foreach ($folders as $folder) {
    try {
      $sync = $emailClass->syncEmails($folder, 0, true);
      if (is_object($sync)) {
        foreach ($sync as $s) {
          $total++;
          //if ($s % 5 === 0) {
            $ctrl->stream([
              'isSynchronizing' => true,
              'synchronized' => $total
            ]);
          //}
        }
      }
      else {
        $total = $sync;
      }
    }
    catch (\Exception $e) {
      X::log($e->getMessage(), "poller_email_error2");
      $ctrl->stream([
        'success' => false,
        'data' => [
          'error' => $e->getMessage()
        ]
      ]);
    }
  }

  ob_end_clean();
  $ctrl->stream([
    'success' => true,
    'total' => $total
  ]);
}
else {
  $ctrl->stream(['success' => false]);
}

