<?php
/*
 * Describe what it does!
 *
 **/

/** @var $model \bbn\Mvc\Model */
$id_accounts = $model->inc->options->fromCode('accounts', 'email', 'appui');
$id_types = $model->inc->options->fromCode('types', 'email', 'appui');

return [
  'types' => array_map(function($a){
    return [
      'value' => $a['code'],
      'text' => $a['text']
    ];
  }, $model->inc->options->fullOptions($id_types)),
  'data' => $model->inc->pref->getAll($id_accounts)
];