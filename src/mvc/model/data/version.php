<?php
/* @var \bbn\Mvc\Model $model */
if ( 
  !empty($model->data['id_note']) && 
  !empty($model->data['version']) &&
  ($notes = new \bbn\Appui\Note($model->db)) &&
  ($version = $notes->getFull($model->data['id_note'], $model->data['version'])) &&
  ($ftype = $model->inc->options->fromCode('file', 'media', 'note', 'appui')) &&
  ($ltype = $model->inc->options->fromCode('link', 'media', 'note', 'appui'))
){
  $version['name'] = $model->db->selectOne([
    'table' => 'bbn_notes_masks',
    'fields' => ['name'],
    'where' => [
      'logic' => 'AND',
      'conditions' => [[
        'field' => 'id_note',
        'operator' => '=',
        'value' => $version['id']
      ]]
      ],
  ]);
  $version['files'] = [];
  $version['links'] = [];
  foreach ( $version['medias'] as $m ){
    if ( $m['type'] === $ftype ){
      $version['files'][] = [
        'id' => $m['id'],
        'name' => $m['name'],
        'title' => $m['title'],
        'extension' => '.'.\bbn\Str::fileExt($m['name'])
      ];
    }
    if ( $m['type'] === $ltype ){
      $version['links'][] = $m;
    }
  }
  unset($version['medias']);
  return [
    'success' => true,
    'data' => $version
  ];
}
return ['success' => false];