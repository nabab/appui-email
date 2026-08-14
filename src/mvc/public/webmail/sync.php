<?php
use bbn\User\Email;
/** @var bbn\Mvc\Controller $ctrl */

if ($email = new Email($ctrl->db)) {
  if (!empty($ctrl->post['add'])) {
    foreach ($ctrl->post['add'] as $idFolder) {
      $email->addQueue([
        'id_folder' => $idFolder,
        'action' => 'sync'
      ]);
    }
  }
  else {
    $ctrl->setStream();
    try {
      $email->startProcessQueue(
        fn($m) => !empty($m['action']) && ($m['action'] === 'ping') ? $ctrl->pingStream() : $ctrl->stream($m)
      );
    }
    catch (Exception $e) {
      bbn\X::log($e->getMessage(), 'webmail_sync');
      $ctrl->stream([
        'success' => false,
        'error' => $e->getMessage(),
        'errorCode' => $e->getCode()
      ]);
    }

    $email->stopProcessQueue();
  }

  $ctrl->stream([
    'success' => true
  ]);
}

