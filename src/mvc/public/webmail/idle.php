<?php
use bbn\User\Email;
/** @var $ctrl \bbn\Mvc\Controller */

$ctrl->setStream();
if (!empty($ctrl->post['account'])
  && ($email = new Email($ctrl->db))
) {
  try {
    $email->idle(
      $ctrl->post['account'],
      fn($m) => $ctrl->stream($m),
      $ctrl
    );
  }
  catch (Exception $e) {
    bbn\X::log($e->getMessage(), 'webmail_idle');
    $ctrl->stream([
      'success' => false,
      'error' => $e->getMessage(),
      'errorCode' => $e->getCode()
    ]);
  }

  $email->stopIdle($ctrl->post['account']);
}

$ctrl->stream([
  'success' => true
]);
