<?php
use bbn\User\Email;
use bbn\X;

if ($model->hasData(['id_account', 'email'], true)
  && !empty($model->data['email']['to'])
  && ( !empty($model->data['email']['title'])
    || !empty($model->data['email']['text']))
) {
  $em = new Email($model->db);
  if ($account = $em->getAccount($model->data['id_account'])) {
    $model->data['email']['from'] = $account['login'];
    return [
      'data' => $model->data['email'],
      'success' => $em->send(
        $model->data['id_account'],
        $model->data['email']
      )
    ];
  }
}

return ['success' => false];
