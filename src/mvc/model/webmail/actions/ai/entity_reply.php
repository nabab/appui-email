<?php
use bbn\X;
use bbn\Str;
use bbn\Appui\Ai;
use bbn\User\Email;
/** @var bbn\Mvc\Model $model */

$ai = new Ai($model->db);
$em = new Email($model->db);
if ($model->hasData(['id', 'id_entity'], true)
  && ($prompt = $ai->getPromptByShortcode('entity-email-reply'))
  && !empty($prompt['settings']['id_model'])
  && ($endpoint = $ai->getEndpointByModel($prompt['settings']['id_model']))
  && ($modelName = X::getField($endpoint['models'], ['id' => $prompt['settings']['id_model']], 'text'))
  && ($email = $em->getEmail($model->data['id']))
  && ($entity = $model->getPluginModel('data/ai/entity_email_reply', [
    'id_entity' => $model->data['id_entity']
  ]))
  && ($idFolder = $em->getEmailFolderId($email['id']))
) {
  $d = [
    "message" => $email['html'] ?: $email['plain'],
    "messages" => [],
    "data" => $entity
  ];
  if (!empty($email['references'])
    && ($idThread = $em->getThreadId($email['id']))
    && ($thread = $em->getThread($idThread, [$idFolder]))
  ) {
    foreach ($thread as $t) {
      if ($t['id'] !== $email['id']) {
        $e = $em->getEmail($t['id']);
        $d['messages'][] = [
          "type" => $email['id_account'] === $e['id_account'] ? "sent" : "received",
          "text" => $e['html'] ?: $e['plain']
        ];
      }
    }
  }

  $ai->setEndpoint($endpoint['data']['id'], $modelName);
  X::log($d, 'ai_entity_reply');
  $res = $ai->getPromptResponse(
    $prompt,
    json_encode($d),
    [
      'model' => $modelName,
      'cfg' => $prompt['settings']['cfg']
    ]
  );
  if (!empty($res['success'])
    && !empty($res['result']['content'])
  ) {
    $res['result']['content'] = trim($res["result"]["content"], "\n\r\t *");
    if (str_starts_with($res['result']['content'], '```html')) {
      $res['result']['content'] = substr($res['result']['content'], 7, -3);
    }

    return [
      'success' => true,
      'data' => Str::text2html(trim($res["result"]["content"], "\n\r\t *"))
    ];
  }
  else if (!empty($res['error'])) {
    return [
      'success' => false,
      'error' => $res['error']
    ];
  }
}

return ['success' => false];