<?php

use bbn\X;
return [];
X::log("Starting email poller");
return [[
  'id' => 'appui-email-0',
  'frequency' => 30,
  'function' => function (array $data) use ($model) {

    $em = new bbn\User\Email($model->db);
    $error = null;
    
    try {
      $accounts = $em->getAccounts();
    } catch (\Exception $e) {
      $error = $e->getMessage();
      X::log($error, "poller_email_error");
      return [
        'success' => false,
        'data' => [
          'error' => $error
        ]
      ];
    }
    $tot = 0;
    X::log("Starting email function");
    foreach ($accounts as $a) {
      if ($a['stage'] == 1) {
        X::log("Checking account ".$a['id'], "test_stage");
        if ($tot < 250) {
          X::map(
            function ($folder) use (&$em, &$a, &$tot) {
              X::log(["POLLER", $folder], "poller_email");
              if ($tot < 250) {
                try {
                  $check = $em->checkFolder($folder);
                } catch (\Exception $e) {
                  X::log($e->getMessage(), "poller_email_error");
                  $check = false;
                }
                if ($check) {
                  try {
                    $tot += $em->syncEmails($folder, 50);
                  } catch (\Exception $e) {
                    X::log($e->getMessage(), "poller_email_error");
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

              return $folder;
            },
            $em->getFolders($a['id']),
            'items'
          );
        }
      } else {
        X::log("Account ".$a['id']." is not ready", "test_stage");
        try {
          $em->syncThreads(100);
        } catch (\Exception $e) {
          X::log($e->getMessage(), "poller_email_error");
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

    // for each accounts check if each folder has all the emails in the db
    $accounts = $em->getAccounts();
    foreach ($accounts as $a) {
      $folders = $em->getFolders($a['id']);
      X::log($folders, "stage_folders");
      $is_same = true;
      // check for each folders if the number of emails in the db is the same as the number of emails in the folder with the uid
      foreach ($folders as $f) {
        $last_uid = $em->getLastUid($f);

        if ($f['db_uid_max'] != $last_uid) {
          $is_same = false;
          break;
        }
      }
      // if the number of emails in the db is the same as the number of emails in the folder with the uid, set the stage to 2 so we can start the threads creation
      if ($is_same && $a['stage'] === 1) {
        $em->setAccountStage($a['id'], 2);
      // else if the number of emails in the db is not the same as the number of emails in the folder with the uid, set the stage to 1 so we can start the emails sync
      } else if (!$is_same && $a['stage'] === 2) {
        $em->setAccountStage($a['id'], 1);
      }
    }

    // Update users mail
    $hashes = $em->getHashes();
    if ($hashes !== $data['hashes']) {
      return [
        'success' => true,
        'data' => $em->getHashes()
      ];
    } else {
      return ['success' => true];
    }
  }
]];