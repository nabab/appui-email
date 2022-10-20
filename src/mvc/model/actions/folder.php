<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\User\Email;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData(['action', 'id_account'], true)) {
  $em = new Email($model->db);
  switch($model->data['action']) {
    case 'delete':
      if ($model->hasData('id')) {
        $success = true;
        $res = [];
   		  foreach ($model->data['id'] as $folder) {
          $tmp =  $em->deleteFolder($folder['id'], $model->data['id_account']);
          if ($success && !$tmp) {
            $success = false;
          }
        	array_push($res, [
            'success' => $tmp,
       			'text' => $folder['text']
          ]);
      	}
        return [
          'success' => $success,
          'res' => $res,
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
      break;
    case 'create':
      if ($model->hasData('name')) {
        return [
          'success' => $em->createFolder($model->data['id_account'], $model->data['name'], $model->data['id_parent'] ?? null),
          'account' => $em->getAccount($model->data['id_account'])
        ];
      }
      return [
        'success' => false,
        'error' => 'Name of folder not given'
      ];
      break;
    default:
      return [
        'success' => false,
        'error' => 'Action given not exist'
      ];
  }
}

return [
  'success' => false,
  'error' => 'No action given in the data request'
];