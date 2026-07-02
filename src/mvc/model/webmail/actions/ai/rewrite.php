<?php
use bbn\X;
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

$ai = new Ai($model->db);
if ($model->hasData(['text', 'style'], true)
  && ($prompt = $ai->getPromptByShortcode('rewrite-text-html'))
  && !empty($prompt['settings']['id_model'])
  && ($endpoint = $ai->getEndpointByModel($prompt['settings']['id_model']))
  && ($modelName = X::getField($endpoint['models'], ['id' => $prompt['settings']['id_model']], 'text'))
  ) {
  $ai->setEndpoint($endpoint['data']['id'], $modelName);
  $prompt['content'] = str_replace("{{bbn_ai_style}}", $model->data['style'], $prompt['content']);
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