<?php

use bbn\Appui\Search;

/** @var bbn\Mvc\Controller $ctrl */
/*
$types_cond = array_values(array_map(
  function($b) {
    return [
      [
        'field' => 'id_type',
        'operator'  => '!=',
        'value' => $b['id']
      ]
    ];
  },
  array_filter($model->inc->options->fullOptions('types', 'note', 'appui'), function($a) {
    return !empty($a['alias']) && ($a['alias']['code'] === 'bbn-cms');
  })
));
$url = $model->pluginUrl('appui-email');
return Search::register(function($search) use ($url, $types_cond) {
  $fields = ['id_note', 'version', 'id_type', 'code', 'type' => 'bbn_options.text', 'title', 'latest', 'match' => "'Found in content'"];
  return [
    'score' => 10,
    'component' => 'appui-note-search-item',
    'url' => $url . '/cms/cat/{{code}}/{{id_note}}',
    'cfg' => [
      'tables' => ['bbn_notes_versions'],
      'fields' => $fields,
      'join' => [
        [
          'table' => 'bbn_notes',
          'on' => [
            [
              'field' => 'id_note',
              'exp' => 'bbn_notes.id'
            ]
          ]
        ],
        [
          'table' => 'bbn_options',
          'on' => [
            [
              'field' => 'id_type',
              'exp' => 'bbn_options.id'
            ]
          ]
        ],
      ],
      'where' => [
        'conditions' => array_merge([
          [
            'field' => 'latest',
            'value' => 1
          ], [
            'field' => 'content',
            'operator' => 'contains',
            'value' => $search
          ],
        ], $types_cond)
      ]
    ],
    'alternates' => [
      [
        'where' => [
          'conditions' => array_merge([
            [
              'field' => 'latest',
              'value' => 0
            ], [
              'field' => 'content',
              'operator' => 'contains',
              'value' => $search
            ]
            ], $types_cond)
        ],
        'score' => 2
      ]
    ]
  ];
});
*/


