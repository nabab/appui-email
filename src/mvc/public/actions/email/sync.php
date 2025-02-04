<?php
use bbn\User\Email;
use bbn\X;

$emailClass = new Email($ctrl->db);
$folders = [];

if (!empty($ctrl->post['id_folder'])) {
  if ($f = $emailClass->getFolder($ctrl->post['id_folder'])) {
    $folders[] = $f;
  }
}
else if (!empty($ctrl->post['id_account'])) {
  if ($accountFolders = $emailClass->getFolders($ctrl->post['id_account'])) {
    $folders = $accountFolders;
  }
}
else if ($accounts = $emailClass->getAccountsIds()) {
  foreach ($accounts as $a) {
    if ($accountFolders = $emailClass->getFolders($a)) {
      $folders = array_push($folders, ...array_values($accountFolders));
    }
  }
}

if (!empty($folders)) {
  $ctrl->setStream();
  $total = 0;
  foreach ($folders as $f) {
    try {
      $check = $emailClass->checkFolder($f);
    }
    catch (\Exception $e) {
      $check = false;
    }

    if ($check) {
      try {
        foreach ($folders as $folder) {
          $sync = $emailClass->syncEmails($folder);
          if (is_object($sync)) {
            foreach ($sync as $s) {
              $total++;
              if ($s % 5 === 0) {
                $ctrl->stream([
                  'isSynchronizing' => true,
                  'synchronized' => $total
                ]);
              }
            }
          }
          else {
            $total = $sync;
          }
        }
      }
      catch (\Exception $e) {
        $error = $e->getMessage();
        return [
          'success' => false,
          'data' => [
            'error' => $error
          ]
        ];
      }
    }
  }

  return [
    'success' => true,
    'total' => $total
  ];
}

return ['success' => false];
