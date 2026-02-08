<?php
use bbn\User\Email;
/** @var $ctrl \bbn\Mvc\Controller */

$ctrl->setStream();
if (!empty($ctrl->post['account'])
  && ($email = new Email($ctrl->db))
  && ($mailbox = $email->getMailbox($ctrl->post['account']))
) {
  try {
    $idle = $mailbox->idle(function($message) use ($ctrl) {
      $ctrl->stream([
        'message' => $message
      ]);
    }, null, $ctrl);
  }
  catch (Exception $e) {
    bbn\X::log($e->getMessage(), 'mirko_idle');
    $ctrl->stream([
      'success' => false,
      'error' => $e->getMessage(),
      'errorCode' => $e->getCode()
    ]);
  }

  if ($mailbox->getIdleStream()) {
    $mailbox->stopIdle();
  }
}

$ctrl->stream([
  'success' => true
]);
