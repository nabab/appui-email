<?php

$ctrl->obj->url= APPUI_EMAIL_ROOT.'page/sent';
$ctrl
  ->setIcon('nf nf-fa-envelope')
  ->setUrl(APPUI_EMAIL_ROOT.'page/sent')
  ->combo(_("e-Mails sent"));