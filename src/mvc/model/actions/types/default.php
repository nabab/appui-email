<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 19:04
 *
 * @var $model \bbn\Mvc\Model
 */
$masks = new \bbn\Appui\Masks($model->db);
if ( !empty($model->data['id_note']) &&
	\bbn\Str::isUid($model->data['id_note']) &&
	$masks->setDefault($model->data['id_note'])
){
	return [
		'success' => true
	];
}
