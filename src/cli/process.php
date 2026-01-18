<?php

use bbn\Appui\Mailing;

$mailing = new Mailing($ctrl->db);

if ($result = $mailing->process(30)) {
  echo sprintf(_("Processed %d mail"), $result);
}
