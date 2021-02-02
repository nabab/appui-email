<?php
$mailing = new \bbn\Appui\Mailing($ctrl->db);
if ( ($result = $mailing->process(30)) && isset($result['sent'], $result['successes']) ){
  echo _("Processed").': '.$result['sent'].PHP_EOL._("Sent").': '.$result['successes'];
}
