<?php

$ctrl->obj->url= APPUI_EMAILS_ROOT.'page/sent';
$ctrl
  ->setIcon('nf nf-fa-envelope')
  ->setUrl(APPUI_EMAILS_ROOT.'page/sent')
  ->combo(_("e-Mails sent"));