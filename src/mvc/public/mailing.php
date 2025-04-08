<?php
use bbn\X;

/** @var bbn\Mvc\Controller $ctrl */
$ctrl->setColor('teal', 'white');
$root = $ctrl->pluginUrl('appui-email') . '/';
if (!defined('BBN_BASEURL') || empty(constant('BBN_BASEURL'))) {
  $ctrl->setUrl($root . 'mailing')
    ->setIcon('nf nf-fa-envelope')
    ->combo(_("eMails"), ['root' => $root, 'page' => $ctrl->arguments[0] ?? '']);
}
else {
  $ctrl->addToObj($ctrl->getPath(), [], true);
}
