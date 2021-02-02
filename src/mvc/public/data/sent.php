<?php
if ( $model = $ctrl->getPluginModel('data/sent', $ctrl->post) ){
  $ctrl->obj = $model;
}
else {
  $ctrl->action();
}