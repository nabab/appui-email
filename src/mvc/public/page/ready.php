<?php
/*
$ctrl->setIcon('nf nf-fa-envelope_open_text');
$d = $ctrl->getPluginModel('page/emails', $ctrl->data);

if ( is_null($d) ){
  $d = $ctrl->getModel($ctrl->data);

}
$ctrl->setTitle($model['title'] ?? _("e-Mails ready"));
$views = $ctrl->getPluginViews('page/emails', $d);
$ctrl->obj->data = $d;
echo $views['html'] ?: $ctrl->getView();
$ctrl->addScript($views['js'] ?: $ctrl->getView('', 'js'));
$ctrl->obj->css = $views['css'] ?: $ctrl->getLess();*/

$ctrl->obj->url = APPUI_EMAIL_ROOT.'page/ready';
$ctrl
  ->setUrl(APPUI_EMAIL_ROOT.'page/ready')
  ->setIcon('nf nf-fa-envelope_o')
  ->combo(_("e-Mails ready"));