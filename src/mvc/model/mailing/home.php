<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 16:02
 *
 * @var $model \bbn\Mvc\Model
 */


//$recipients = $model->inc->options->fullOptions($model->inc->options->fromCode('emails_listes'));
return [
  'root' => APPUI_EMAIL_ROOT,
  'root_usergroup' => $model->pluginUrl('appui-usergroup').'/',
  'types' => $model->db->getRows("
    SELECT bbn_notes_masks.id_note AS id, bbn_notes_masks.name AS text
    FROM bbn_notes_masks
      JOIN bbn_notes_versions
        ON bbn_notes_versions.id_note = bbn_notes_masks.id_note
      LEFT JOIN bbn_notes_versions AS v_old
        ON (bbn_notes_versions.id_note = bbn_notes_masks.id_note
        AND bbn_notes_versions.version > v_old.version)
    WHERE v_old.id_note IS NULL
    GROUP BY bbn_notes_masks.id_note
    ORDER BY text
  "),
  'count' => $model->getModel(APPUI_EMAIL_ROOT.'data/count'),
  'recipients' => array_map(
    fn($a) => [
      'text' => $a['text'],
      'value' => $a['id'],
      'code' => $a['code'],
    ],
    array_values(array_filter(
      $model->inc->options->fullOptions('emails_listes'),
      fn($a) => empty($a['archived'])
    ))
  ),
  'senders' => array_map(function($a){
    return [
      'text' => $a['text'],
      'value' => $a['id'],
      'desc' => $a['desc']
    ];
  }, $model->inc->options->fullOptions('sender', 'mailing', 'appui'))
];
