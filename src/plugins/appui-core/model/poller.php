<?php
/** @todo You have an infinte loop of the poller here */
return [];
use bbn\X;

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

      if ($a['stage'] === 1) {
        $f = $em->getFolders($a['id']);

        // set INBOX as the first folder to sync

        if ($tot < 100) {
          X::map(
            function ($folder) use (&$em, &$a, &$tot) {

              if ($tot < 100) {
                try {
                  $check = $em->checkFolder($folder);
                } catch (\Exception $e) {
                  X::log($e->getMessage(), "poller_email_error");
                  $check = false;
                }
                if ($check) {
                  try {
                    $tot += $em->syncEmails($folder, 25);
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
            $f,
            'items'
          );
        }
      } else {

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
    $sync = [];
    foreach ($accounts as $a) {
      $folders = $em->getFolders($a['id']);
      $sync[$a['id']] = [
        'name' => $a['login'],
        'id' => $a['id']
      ];

      X::log($folders, "stage_folders");
      $is_same = true;
      // check for each folders if the number of emails in the db is the same as the number of emails in the folder with the uid
      foreach ($folders as $f) {

        $last_uid = $em->getLastUid($f);
        $first_uid = $em->getFirstUid($f);

        if ($f['db_uid_max'] === null || $f['db_uid_min'] === null && $f['last_uid'] !== null) {
          $is_same = false;
          break;
        }

        if ($f['db_uid_max'] != $last_uid && $f['db_uid_min'] != $first_uid) {
          $is_same = false;
          break;
        }
      }

      foreach ($folders as $f) {
        $folder = $em->getFolder($f['id']);
        $info = $em->getInfoFolder($f['id']);
        $sync[$a['id']]['folders'][$f['id']] = [
          'id' => $f['id'],
          'name' => $folder['uid'],
          'db_msg' => $folder['num_msg'],
          'msg' => $info->Nmsgs,
        ];
      }
      // if the number of emails in the db is the same as the number of emails in the folder with the uid, set the stage to 2 so we can start the threads creation
      if ($is_same && $a['stage'] === 1) {
        $em->setAccountStage($a['id'], 2);
        X::log("Account ".$a['id']." is ready", "test_stage");
      // else if the number of emails in the db is not the same as the number of emails in the folder with the uid, set the stage to 1 so we can start the emails sync
      } else if (!$is_same && $a['stage'] === 2) {
        $em->setAccountStage($a['id'], 1);
        X::log("Account ".$a['id']." is not ready", "test_stage");
      }
    }

    // Update users mail
    $hashes = $em->getHashes();
    X::log($data, "poller_email_data");
    if (!isset($data['hashes']) || ($hashes !== $data['hashes'])) {
      return [
        'success' => true,
        'data' => [
          'hashes' => $hashes,
          'sync' => $sync
        ]
      ];
    } else {
      return ['success' => true];
    }
  }
]];
