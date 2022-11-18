<?php

/** @var $ctrl \bbn\Mvc\Controller */

if (empty($ctrl->post)) {
	$ctrl->combo(_('My contacts'));
}
else {
  try {
    $search = $ctrl->post['filters']['conditions'][0]['value'];
  } catch (\Exception $e) {
    
  }
  if ($search) {
    $ctrl->post['filters'] = [
      'logic' => "OR",
      'conditions' => [
        [
          'field' => 'name',
          'operator' => 'contains',
          'value' => $search
        ],
        [
          'field' => 'email',
          'operator' => 'contains',
          'value' => $search
        ]
      ]
    ];
  }
  $ctrl->action();
}