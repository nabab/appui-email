<?php
use bbn\X;

/** @var bbn\Mvc\Model $model */
if ($model->hasData('action')) {
  switch ($model->data['action'])
  {
    case 'test':
    case 'save':
      if ($model->hasData(['type', 'login', 'pass', 'email'], true)
          && ($code = $model->inc->options->code($model->data['type']))
      ) {
        $cfg = [
          'type' => $code,
          'login' => $model->data['login'],
          'host' => $model->data['host'] ?? null,
          'pass' => $model->data['pass'],
          'ssl' => $model->data['ssl'] ?? null
        ];
        try {
          $mb = new bbn\Appui\Mailbox($cfg);
        }
        catch (\Exception $e) {
          return ['error' => $e->getMessage()];
        }

        if ($mb->getStatus() === 'ok') {
          if ($model->data['action'] === 'test') {
            $model->data['res']['success'] = true;
            $res = [];
            $subscribed = $mb->listAllSubscribed();
            $mbParam = $mb->getParams();
            $put_in_res = function (array $a, &$res, $prefix = '') use (&$put_in_res, &$subscribed, $mbParam) {
              $ele = array_shift($a);
              $idx = X::find($res, ['text' => $ele]);
              if (null === $idx) {
                $idx   = count($res);
                $res[] = [
                  'text' => $ele,
                  'uid' => $prefix.$ele,
                  'items' => [],
                  'subscribed' => in_array($mbParam.$prefix.$ele, $subscribed)
                ];
              }

              if (count($a)) {
                $put_in_res($a, $res[$idx]['items'], $prefix.$ele.'.');
              }
            };

            foreach ($mb->listAllFolders() as $dir) {
              $tmp = str_replace($mbParam, '', $dir);
              $bits = X::split($tmp, '.');
              $put_in_res($bits, $res);
            }
            $model->data['res']['data'] = $res;
            break;
          }
          elseif ($model->hasData(['email', 'folders'], true)
            && is_array($model->data['folders'])
          ) {
            unset($mb);
            $em = new bbn\User\Email($model->db);
            $cfg['folders'] = $model->data['folders'];
            $cfg['email'] = $model->data['email'];
            try {
              if ($id_account = $em->addAccount($cfg)) {
                unset($em);
                $em = new bbn\User\Email($model->db, $model->inc->user, $model->inc->pref);
                return [
                  'success' => true,
                  'data' => $em->getAccounts(),
                  'id_account' => $id_account
                ];
              }
            }
            catch (\Exception $e) {
              return ['error' => $e->getMessage()];
            }
          }
        }
        else {
          return ['error' => $mb->getStatus()];
        }
      }
      break;

    case 'delete':
      $em = new bbn\User\Email($model->db, $model->inc->user, $model->inc->pref);
      if ($model->hasData(['data'], true) && $model->data["data"]["id"]) {
        $id = $model->data["data"]['id'];
        if ($em->getAccount($id)) {
          return [
            'success' => $em->deleteAccount($id)
          ];
        }
        return [
          'success' => false,
          'error' => 'Account does not exist'
        ];
      }
      return [
        'success' => false,
        'error' => 'id object not found in the data request'
      ];
      break;
    case 'update':
      break;
    case 'insert':
      break;
    case 'get':
      $em = new bbn\User\Email($model->db, $model->inc->user, $model->inc->pref);
      return [
        'account' => $em->getAccount($model->data['id'])
      ];
  }
}
return $model->data['res'];