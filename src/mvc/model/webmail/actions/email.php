<?php
use bbn\User\Email;
use bbn\X;
use bbn\Appui\Task;

if ($model->hasData('action', true)) {
  $em = new Email($model->db);
  switch ($model->data['action']) {
    case 'move':
      if ($model->hasData(['id', 'id_folder'], true)) {
        try {
          $success = $em->moveEmailToFolder($model->data['id'], $model->data['id_folder']);
          return [
            'success' => $success
          ];
        }
        catch (Exception $e) {
          return [
            'success' => false,
            'error' => $e->getMessage()
          ];

        }
      }

      break;
    case 'send':
      if ($model->hasData(['id_account', 'email'], true)
        && !empty($model->data['email']['to'])
        && ( !empty($model->data['email']['title'])
          || !empty($model->data['email']['text']))
        && ($account = $em->getAccount($model->data['id_account']))
      ) {
        $model->data['email']['from'] = $account['login'];
        return [
          'data' => $model->data['email'],
          'success' => $em->send(
            $model->data['id_account'],
            $model->data['email']
          )
        ];
      }

      break;
    case 'draft':
      if ($model->hasData(['id_account', 'email'], true)
        && ($account = $em->getAccount($model->data['id_account']))
      ) {
        $model->data['email']['from'] = $account['login'];
        $idDraftsFolder = null;
        if (!empty($account['rules']['drafts'])) {
          $idDraftsFolder = X::getField($account['folders'], ['uid' => $account['rules']['drafts']], 'id');
        }

        try {
          if ($model->hasData('id', true)) {
            $em->deleteEmail($model->data['id']);
          }
          else if ($model->hasData('uid', true)
            && !empty($idDraftsFolder)
          ) {
            $em->deleteEmail($em->getEmailIdByUniqueId($model->data['uid'], $idDraftsFolder));
          }

          if ($mailUid = $em->saveDraft(
            $model->data['id_account'],
            $model->data['email']
          )) {
            $id = null;
            if (!empty($idDraftsFolder)) {
              $sync = $em->syncEmails($idDraftsFolder, 0, true);
              $synchronized = 0;
              if (is_object($sync)) {
                foreach ($sync as $s) {
                  $synchronized++;
                }
              }
              else {
                $synchronized = $sync;
              }

              $id = $em->getEmailIdByUniqueId($mailUid, $idDraftsFolder);
            }

            return [
              'id' => $id,
              'uid' => $mailUid,
              'success' => true
            ];
          }
        }
        catch (\Exception $e) {
          return [
            'success' => false,
            'error' => $e->getMessage()
          ];
        }
      }

      break;
    case 'task':
      if ($model->hasData(['id_email', 'id_task'], true)
        && ($email = $em->getEmail($model->data['id_email']))
        && ($taskCls = new Task($model->db))
      ) {
        $taskCfg = $taskCls->getCfg($model->data['id_task']);
        if (!isset($taskCfg['email'])) {
          $taskCfg['email'] = [];
        }

        if (!in_array($email['msg_unique_id'], $taskCfg['email'])) {
          $taskCfg['email'][] = $email['msg_unique_id'];
        }

        $taskCls->setCfg($model->data['id_task'], $taskCfg);
        return [
          'success' => $taskCls->update(
            $model->data['id_task'],
            'content',
            $email['html']
          )
        ];
      }
      break;
    case 'subtask':
      break;
    case 'tasknote':
      break;
  }
}

return [
  'success' => false,
];