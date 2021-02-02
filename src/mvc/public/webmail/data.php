<?php
/*
 * Describe what it does!
 *
 * @var $ctrl \bbn\Mvc\Controller 
 *
 */
if (isset($ctrl->post['data'], $ctrl->post['data']['id_folder'])) {
  $ctrl->addData(['id_folder' => $ctrl->post['data']['id_folder']])
    ->action();
}