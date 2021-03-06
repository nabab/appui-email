<?php
/** @var \bbn\Mvc\Model $model */
if ( isset($model->data['limit']) ){
  $grid = new \bbn\Appui\Grid($model->db, $model->data, [
    'table' => 'bbn_emails',
    'fields' => [
      'bbn_emails.id',
      'bbn_emails.email',
      'bbn_emails.id_mailing',
      'subject' => 'IFNULL(bbn_emails.subject, bbn_notes_versions.title)',
      'bbn_emails.cfg',
      'bbn_emails.status',
      'bbn_emails.delivery',
      'bbn_emails.read', 
      'bbn_emails.priority',
      'bbn_emailings.id_note'
    ],
    'join' => [[
      'table' => 'bbn_emailings',
      'type' => 'left',
      'on' => [[
        'field' => 'bbn_emails.id_mailing',
        'exp' => 'bbn_emailings.id'
      ]]
    ], [
      'table' => 'bbn_notes_versions',
      'type' => 'left',
      'on' => [[
        'field' => 'bbn_emailings.id_note',
        'exp' => 'bbn_notes_versions.id_note'
      ], [
        'field' => 'bbn_emailings.version',
        'exp' => 'bbn_notes_versions.version'
      ]]
    ]], 
    'order' => [[
      'field' => 'bbn_emails.delivery',
      'dir' => 'DESC'
    ]],
    'count' => "SELECT COUNT(bbn_emails.id) FROM bbn_emails LEFT JOIN bbn_emailings ON bbn_emailings.id = bbn_emails.id_mailing"
  ]);
  
  if ( $grid->check() ){
    $note = new \bbn\Appui\Note($model->db);
    $tmp_grid = $grid->getDatatable();
    $tmp_grid['data'] = array_map(function($a)use($note){
      if(!empty($a['id_note'])){
        $a['attachments'] = $note->getMedias($a['id_note']);
        return $a;
      }
      return $a;
    }, $tmp_grid['data']);
    return $tmp_grid;
  }
}