<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 16:02
 *
 * @var $model \bbn\Mvc\Model
 */

if ( !empty($model->data['id']) ){
  $notes = new \bbn\Appui\Note($model->db);
  if ( ($mail = $model->db->rselect('bbn_emailings', ['id_note', 'version'], ['id' => $model->data['id']])) &&
    ($note = $notes->get($mail['id_note'], $mail['version'], true))
  ){
    return $note;
  }
}
