<?php
use bbn\X;

X::log("Starting email poller");
return [[
  'id' => 'appui-email-0',
  'frequency' => 30,
  'function' => function(array $data) use($model){
    $em = new bbn\User\Email($model->db);
    $accounts = $em->getAccounts();
    $tot = 0;
    X::log("Starting email function");
    foreach ($accounts as $a) {
      if ($tot < 50) {
        X::map(
          function ($folder) use (&$em, &$a, &$tot) {
            X::log(["POLLER", $folder], "poller_email");
            if ($tot < 50) {
              $check = $em->checkFolder($folder);
              if ($check) {
                $tot += $em->syncEmails($folder, 50);
                X::log('hello from poller mail '.$tot);
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
    return ['success' => true];
  }
]];