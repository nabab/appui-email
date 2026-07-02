<?php
use bbn\X;
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

$ai = new Ai($model->db);
if ($model->hasData('text', true)
  && ($prompt = $ai->getPromptByShortcode('correct-text-html'))
  && !empty($prompt['settings']['id_model'])
  && ($endpoint = $ai->getEndpointByModel($prompt['settings']['id_model']))
  && ($modelName = X::getField($endpoint['models'], ['id' => $prompt['settings']['id_model']], 'text'))
  ) {
  $ai->setEndpoint($endpoint['data']['id'], $modelName);
  $res = $ai->getPromptResponse(
    $prompt,
    $model->data['text'],
    [
      'model' => $modelName,
      'cfg' => $prompt['settings']['cfg']
    ]
  );
  if (!empty($res['success'])
    && !empty($res['result']['content'])
  ) {
    if (str_starts_with($res['result']['content'], '```html')) {
      $res['result']['content'] = substr($res['result']['content'], 7, -3);
    }

    return [
      'success' => true,
      'data' => $res['result']['content']
    ];
  }
}

return ['success' => false];