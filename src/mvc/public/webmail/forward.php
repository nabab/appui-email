<?php
/*
       * Describe what it does!
       *
       * @var $ctrl \bbn\Mvc\Controller 
       *
       */
if ($ctrl->hasArguments()) {
  $ctrl->addData(['id' => $ctrl->arguments[0]])->combo('$subject', true);
}


