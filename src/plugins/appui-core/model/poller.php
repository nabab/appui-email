<?php
use bbn\X;

return [[
  'id' => 'appui-email-0',
  'frequency' => 30,
  'function' => function(array $data) use($model){
    $em = new bbn\User\Email($model->db);
    $accounts = $em->getAccounts();
    $tot = 0;
    foreach ($accounts as $a) {
      if ($tot < 500) {
        X::map(
          function ($folder) use (&$em, &$a, &$tot) {
            if ($tot < 500) {
              $check = $em->checkFolder($folder);
              if ($check) {
                $tot += $em->syncEmails($folder, 100);
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