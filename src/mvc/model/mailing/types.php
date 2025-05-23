<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 14:39
 *
 */

$mnotes = new \bbn\Appui\Masks($model->db);
$masks = array_map(function($a){
  $a['content'] = '';
  return $a;
}, $mnotes->getAll());

return [
  'root' => APPUI_EMAIL_ROOT,
  'is_dev' => $model->inc->user->isDev(),
  'categories' => $masks,
  'empty_categories' => $model->db->rselectAll([
    'tables' => 'bbn_options',
      'fields' => [
        'bbn_options.id',
        'bbn_options.code',
        'bbn_options.text'
      ],
      'join' => [[
        'table' => 'bbn_notes_masks',
        'type' => 'left',
        'on' => [
          'conditions' => [[
            'field' => 'bbn_options.id',
            'exp' => 'bbn_notes_masks.id_type'
          ]]
        ]
      ]],
      'where' => [
        'conditions' => [[
          'field' => 'bbn_notes_masks.id_type',
          'operator' => 'isnull'
        ],[
          'field' => 'bbn_options.id_parent',
          'value' => $model->inc->options->fromCode('options', 'masks', 'appui')
        ]]
      ]

  ])
];