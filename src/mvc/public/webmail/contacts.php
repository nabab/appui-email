<?php

/** @var bbn\Mvc\Controller $ctrl */

if (empty($ctrl->post)) {
	$ctrl->combo(_('My contacts'));
}
else {
  if (!empty($ctrl->post['filters']['conditions'][0]['value'])) {
    $search = trim($ctrl->post['filters']['conditions'][0]['value']);
    $search = trim($search, ';');
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
