<?php
$r = [
  'success' => false,
  'template'=> []
];

if ( isset($model->data['id']) ){
  $masks = new \bbn\Appui\Masks($model->db);
  $template = $masks->get($model->data['id']);
  if ( $template ){
    $r['success'] = true;
    $r['template'] = $template;
  }
}
return $r;
