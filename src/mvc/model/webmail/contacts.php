<?php
/**
 * What is my purpose?
 *
 **/

/** @var $model \bbn\Mvc\Model*/



$cfg = [
  'tables' => [
    'bbn_users_contacts_links'
  ],
  'fields' => [
    'email' => 'value',
    'id' => 'bbn_users_contacts_links.id',
    'id_contact',
    'num_sent',
    'last_sent',
    'name',
    'displayName' => "IFNULL(name, value)"
  ],
  'join' => [
    [
      'table' => 'bbn_users_contacts',
      'on' => [
        [
          'field' => 'id_contact',
          'exp' => 'bbn_users_contacts.id'
        ]
      ]
    ]
  ],
  'filters' => [
    [
      'field' => 'id_user',
      'value' => $model->inc->user->getId(),
    ],
    [
      'field' => 'type',
      'value' => 'email'
    ]
  ]
];

$grid = new bbn\Appui\Grid($model->db, $model->data, $cfg);

if ($grid->check()) {
  return $grid->getDataTable();
}
else {
  return [
    'error' => _('Impossible to retrieve contacts')
  ];
}