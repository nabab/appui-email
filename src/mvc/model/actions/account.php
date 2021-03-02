<?php
/*
 * Describe what it does!
 *
 **/
use bbn\X;

/** @var $model \bbn\Mvc\Model */
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
            if ($id_account = $em->addAccount($cfg)) {
              $model->data['res']['success'] = true;
            }
          }
        }
        else {
          return ['error' => $mb->getStatus()];
        }
      }
      break;

    case 'delete':
      break;
    case 'update':
      break;
    case 'insert':
      break;
  }
}
return $model->data['res'];