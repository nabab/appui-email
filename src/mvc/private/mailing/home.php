<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 15:59
 *
 * @var $ctrl \bbn\Mvc\Controller
 */
//if the page home set the url the router(nav) will be doubled or tripled!!  the forcing you see when you reload the page is only on the selectedNode of the tree
$ctrl->addData(['root' => $ctrl->pluginUrl('appui-email') . '/'])
  ->setUrl($ctrl->data['root'] . 'mailing/home')
  ->combo(_("Mailings"), true);