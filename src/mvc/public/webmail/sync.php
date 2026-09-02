<?php
use bbn\User\Email;
/** @var bbn\Mvc\Controller $ctrl */

if ($email = new Email($ctrl->db)) {
  if (array_key_exists('folders', $ctrl->post)) {
    $res = ['success' => false];
    if (!empty($ctrl->post['folders'])) {
      foreach ($ctrl->post['folders'] as $idFolder) {
        if ($email->addQueue($idFolder)) {
          $res['success'] = true;
        }
      }
    }

    $ctrl->obj->data = $res;
  }
  else {
    $ctrl->setStream();
    try {
      $email->startProcessQueue(
        'sync',
        fn($m) => !empty($m['action']) && ($m['action'] === 'ping')
          ? $ctrl->pingStream()
          : $ctrl->stream($m)
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
    $ctrl->stream([
      'success' => true
    ]);
  }
}

