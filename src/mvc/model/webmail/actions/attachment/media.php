<?php
use bbn\Appui\Medias;
use bbn\User\Email;
use bbn\X;
use bbn\File\Dir;

if ($model->hasData('id', true)) {
  $mediaCls = new Medias($model->db);
  $emailCls = new Email($model->db);
  $email = $emailCls->getEmail($model->data['id']);
  $description = X::_("Attachment of \"%s\" from \"%s\" at %s", $email['subject'], $email['senderaddress'], $email['date']);
  $isPvt = ($model->data['mode'] === 'private_media')
    || ($model->data['mode'] === 'private_media_all');
  $path = $model->userTmpPath(null, 'appui-email');
  switch ($model->data['mode']) {
    case 'private_media':
    case 'shared_media':
      if ($model->hasData('filename', true)
        && ($filename = $model->data['filename'])
        && ($f = $emailCls->getAttachments($model->data['id'], $filename))
      ) {
        if (Dir::createPath($path)
          && file_put_contents($path.$filename, base64_decode($f['data']))
          && $mediaCls->insert($path.$filename, null, $filename, 'file', $isPvt, $description)
        ) {
          if (is_file($path.$filename)) {
            Dir::delete($path.$filename);
          }

          return [
            'success' => true
          ];
        }
      }
      break;
    case 'shared_media_all':
    case 'private_media_all':
      if ($files = $emailCls->getAttachments($model->data['id'], $filename)) {
        $added = 0;
        foreach ($files as $f) {
          $filename = $f['name'];
          if (Dir::createPath($path)
            && file_put_contents($path.$filename, base64_decode($f['data']))
            && $mediaCls->insert($path.$filename, null, $filename, 'file', $isPvt, $description)
          ) {
            $added++;
            if (is_file($path.$filename)) {
              Dir::delete($path.$filename);
            }
          }
        }

        return [
          'success' => $added === count($files)
        ];
      }
      break;
  }
}

return [
  'success' => false
];