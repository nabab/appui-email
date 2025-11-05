<?php
// Temp case - when we talk insert, not update
if (!empty($ctrl->post['file'])
  && !empty($ctrl->post['timestamp'])
) {
  $f = $ctrl->post['file'];
  $new = \bbn\Str::encodeFilename($f, \bbn\Str::fileExt($f));
  if (is_file($ctrl->userTmpPath().$ctrl->post['timestamp'].'/'.$new)
    && unlink($ctrl->userTmpPath().$ctrl->post['timestamp'].'/'.$new)
  ) {
    $ctrl->obj->success = true;
  }
}
