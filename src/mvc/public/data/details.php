<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 17:46
 *
 * @var $ctrl \bbn\Mvc\Controller
 */

if ( !empty($ctrl->post['data']['id']) && \bbn\Str::isUid($ctrl->post['data']['id']) ){
	$ctrl->action();
 }