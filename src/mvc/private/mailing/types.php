<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 13:36
 *
 * @var $ctrl \bbn\Mvc\Controller
 */
$ctrl->addData(['root' => $ctrl->pluginUrl('appui-email') . '/'])
  ->setUrl($ctrl->data['root'] . 'types')
  ->setIcon('nf nf-fa-list')
  ->combo(_("Letters Types"), true);