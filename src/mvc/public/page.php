<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 21/03/2018
 * Time: 20:01
 *
 * @var $ctrl \bbn\Mvc\Controller
 */

$ctrl->obj->url = APPUI_EMAIL_ROOT . 'page';

$ctrl->setColor('teal', 'white')
	->setIcon('nf nf-fa-envelope')
	->combo(_("eMails"), ['root' => APPUI_EMAIL_ROOT, 'page' => $ctrl->arguments[0]]);