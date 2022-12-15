<?php
use bbn\X;
return [];
X::log("Starting email poller");
return [[
  'id' => 'appui-email-0',
  'frequency' => 30,
  'function' => function(array $data) use($model) {

    $em = new bbn\User\Email($model->db);
    $error = null;
    try {
      $accounts = $em->getAccounts();
    } catch (\Exception $e) {
      $error = $e->getMessage();
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
      if ($tot < 250) {
        X::map(
          function ($folder) use (&$em, &$a, &$tot) {
            X::log(["POLLER", $folder], "poller_email");
            if ($tot < 250) {
              try {
                $check = $em->checkFolder($folder);
              } catch (\Exception $e) {
                $check = false;
              }
              if ($check) {
                try {
                  $tot += $em->syncEmails($folder, 50);
                  X::log(["POLLER", $folder, $tot], "startFromUID");
                } catch (\Exception $e) {
                  X::log($e->getMessage(), "poller_email");
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
