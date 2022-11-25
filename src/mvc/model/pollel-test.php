<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/


$em = new bbn\User\Email($model->db);
$accounts = $em->getAccounts();
$tot = 0;
X::log("Starting email function");
foreach ($accounts as $a) {
  if ($tot < 5) {
    X::map(
      function ($folder) use (&$em, &$a, &$tot) {
        if ($tot < 5) {
          $check = $em->checkFolder($folder);
          if ($check) {
            $tot += $em->syncEmails($folder, 5);
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