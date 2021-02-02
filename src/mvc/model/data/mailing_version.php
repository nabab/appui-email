<?php
/* @var \bbn\Mvc\Model $model */

if ( 
  !empty($model->data['id_note']) && 
  !empty($model->data['version']) &&
  ($notes = new \bbn\Appui\Note($model->db)) &&
  ($version = $notes->getFull($model->data['id_note'], $model->data['version'])) &&
  ($ftype = $model->inc->options->fromRootCode('file', 'media', 'note', 'appui')) &&
  ($ltype = $model->inc->options->fromRootCode('link', 'media', 'note', 'appui'))
){
  $mailing = $model->db->rselect([
    'table' => 'bbn_emailings',
    'fields' => [],
    'where' => [
      'logic' => 'AND',
      'conditions' => [[
        'field' => 'id_note',
        'operator' => '=',
        'value' => $version['id']
      ],[
        'field' => 'version',
        'operator' => '=',
        'value' => $version['version']
      ]]
    ],

  ]);
  $version['files'] = [];
  $version['links'] = [];
  
  if ( !empty($mailing['sender'])){
    $version['sender'] = $mailing['sender'];
  }
  if(!empty($mailing['recipients'])){
    $version['recipients'] = $mailing['recipients'];
  }
  
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