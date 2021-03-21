<?php
/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 16:02
 *
 * @var $ctrl \bbn\Mvc\Controller
 */
if ( !empty($ctrl->arguments[0]) && \bbn\Str::isUid($ctrl->arguments[0]) ){
  $ctrl->data = [
    'id' => $ctrl->arguments[0],
    'root' => APPUI_EMAIL_ROOT,
  ];
  if ( $model = $ctrl->getModel() ){
    if ( !empty($model['title']) && (strlen($model['title']) > 20) ){
      $model['title'] = substr($model['title'], 0, 20) . '...';
    }
    echo $ctrl
      ->setIcon('nf nf-fa-th_list')
      ->setUrl(APPUI_EMAIL_ROOT.'page/details/'.$ctrl->arguments[0])
      ->setTitle($model['title'] ?: _('Untitled'))
      ->addJs($ctrl->data)
      ->getView();
  }
}
else{
  return false;
  //$ctrl->obj->url = APPUI_EMAIL_ROOT.'page/details/';
  $ctrl->combo();
}
