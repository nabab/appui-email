<?php
use bbn\X;
use bbn\User\Email;
use bbn\Appui\Mailbox;

/** @var bbn\Mvc\Model $model */
if ($model->hasData('action')) {
  $em = new Email($model->db);
  switch ($model->data['action'])
  {
    case 'test':
    case 'save':
      if ($model->hasData(['type', 'login', 'pass', 'email', 'smtp', 'port'], true)
        && $model->hasData(['encryption', 'locale', 'validatecert'])
        && ($code = $model->inc->options->code($model->data['type']))
      ) {
        $cfg = [
          'type' => $code,
          'login' => $model->data['login'],
          'host' => $model->data['host'] ?? null,
          'pass' => $model->data['pass'],
          'encryption' => !empty($model->data['encryption']) ? 1 : 0,
          'validatecert' => !empty($model->data['validatecert']) ? 1 : 0,
          'port' => $model->data['port'],
          'smtp' => $model->data['smtp'],
          'locale' => $model->hasData('locale', true)
        ];
        try {
          $mb = new Mailbox($cfg);
          $folderTypes = $em->getFolderTypes();
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
            $put_in_res = function (
              array $a,
              &$res,
              $prefix = ''
            ) use (
              &$put_in_res,
              &$subscribed,
              $mbParam,
              $folderTypes
            ) {
              $ele = array_shift($a);
              $idx = X::search($res, ['uid' => $prefix.$ele]);
              if (null === $idx) {
                $idx   = count($res);
                $idOpt = X::getField($folderTypes, ['code' => 'folders'], 'id');
                if (empty($prefix)) {
                  foreach ($folderTypes as $type) {
                    if (!empty($type['names'])) {
                      if (in_array(
                        strtolower($ele),
                        array_map(fn($n) => strtolower($n), $type['names']),
                        true
                      )) {
                        $idOpt = $type['id'];
                        break;
                      }
                    }
                  }
                }

                $res[] = [
                  'text' => $ele,
                  'uid' => $prefix.$ele,
                  'id_option' => $idOpt,
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
          elseif ($model->hasData(['email', 'folders', 'rules'], true)
            && is_array($model->data['folders'])
          ) {
            unset($mb);
            $cfg['folders'] = $model->data['folders'];
            $cfg['email'] = $model->data['email'];
            $cfg['rules'] = $model->data['rules'];
            try {
              if ($id_account = $em->addAccount($cfg)) {
                return [
                  'success' => true,
                  'data' => $em->getAccount($id_account, true),
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
      if ($model->hasData('id', true)) {
        if ($em->getAccount($model->data['id'])) {
          return [
            'success' => $em->deleteAccount($model->data['id'])
          ];
        }

        return [
          'success' => false,
          'error' => X::_('Account does not exist')
        ];
      }

      return [
        'success' => false,
        'error' => X::_('id object not found in the data request')
      ];
      break;

    case 'update':

      break;

    case 'get':
      return [
        'account' => $em->getAccount($model->data['id'])
      ];
  }
}
return $model->data['res'];