<?php
use bbn\User\Email;
/** @var $ctrl \bbn\Mvc\Controller */

if (!empty($ctrl->post['start'])) {
  if (($email = new Email($ctrl->db))
    && ($mailbox = $email->getMailbox($ctrl->post['start']))
    && !$mailbox->getIdleStream()
  ) {
    $mailbox->idle();
  }
}
elseif (!empty($ctrl->post['stop'])) {
  if (($email = new Email($ctrl->db))
    && ($mailbox = $email->getMailbox($ctrl->post['stop']))
    && $mailbox->getIdleStream()
  ) {
    $mailbox->stopIdle();
  }
}
else {
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
      });
    }
    catch (Exception $e) {
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
}