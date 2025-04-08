<?php

$ctrl->addData(['root' => $ctrl->pluginUrl('appui-email') . '/'])
  ->setIcon('nf nf-fa-envelope')
  ->setUrl($ctrl->data['root'] . 'page/sent')
  ->combo(_("e-Mails sent"));