<?php
use bbn\Appui\Grid;
use bbn\User\Email;
use bbn\X;
/** @var bbn\Mvc\Model $model */

$cfg = [
  'tables' => [
    'bbn_users_contacts_links'
  ],
  'fields' => [
    'email' => 'bbn_users_contacts_links.value',
    'id' => 'bbn_users_contacts_links.id',
    'bbn_users_contacts_links.id_contact',
    'bbn_users_contacts_links.num_sent',
    'bbn_users_contacts_links.last_sent',
    'bbn_users_contacts.name',
    'displayName' => "IFNULL(bbn_users_contacts.name, bbn_users_contacts_links.value)"
  ],
  'join' => [[
    'table' => 'bbn_users_contacts',
    'on' => [[
      'field' => 'bbn_users_contacts_links.id_contact',
      'exp' => 'bbn_users_contacts.id'
    ]]
  ]],
  'filters' => [[
    'field' => 'bbn_users_contacts.id_user',
    'value' => $model->inc->user->getId(),
  ], [
    'field' => 'bbn_users_contacts_links.type',
    'value' => 'email'
  ]]
];

$grid = new Grid($model->db, $model->data, $cfg);
if ($grid->check()) {
  $data = $grid->getDataTable();
  $email = new Email($model->db);
  if ($email->hasLocaleDb()) {
    $grid = new Grid($email->getLocaleDb(), $model->data, $cfg);
    if ($grid->check()) {
      $data2 = $grid->getDataTable();
      foreach ($data2['data'] as $d) {
        if (!is_null($idx = X::find($data['data'], ['email' => $d['email']]))) {
          $data['data'][$idx]['num_sent'] += $d['num_sent'];
          if ($data['data'][$idx]['last_sent'] < $d['last_sent']) {
            $data['data'][$idx]['last_sent'] = $d['last_sent'];
          }
        }
        else {
          $data['data'][] = $d;
          $data['total']++;
        }
      }
    }
  }

  return $data;
}
else {
  return [
    'error' => _('Impossible to retrieve contacts')
  ];
}