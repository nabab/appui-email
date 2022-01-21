<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 16:28
 *
 * @var $model \bbn\Mvc\Model
 */

if ( !empty($model->data['id_note']) ){
  $masks = new \bbn\Appui\Masks($model->db);
  if ( $mask = $masks->get($model->data['id_note']) ){
    if($last_creator = $model->db->selectOne([
      'table' => 'bbn_notes_versions',
      'fields' => ['id_user'],
      'where' => [
        'logic' => 'AND',
        'conditions' => [[
          'field' => 'id_note',
          'operator' => '=',
          'value' => $mask['id_note']
        ], [
          'field' => 'latest',
          'operator' => '=',
          'value' => 1
        ]]
      ],
    ])
    ){
      $mask['creator'] = $last_creator;
        return [
          'success' => true,
          'data' => $mask
        ];
      }
    }
}