<?php
/** @var bbn\Mvc\Controller $ctrl */
if (isset($ctrl->post['data'], $ctrl->post['data']['id_folder'])) {
  $ctrl->addData(['id_folder' => $ctrl->post['data']['id_folder']])
    ->action();
}