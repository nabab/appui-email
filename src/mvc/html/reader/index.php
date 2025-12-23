<?= $html ?: nl2br($plain) ?>
<?php if (!empty($quote)) { ?>
  <div id="_bbn_show_quote"
       style="cursor: pointer">
    <?= _("Show quote") ?>
  </div>
  <div id="_bbn_hide_quote"
       style="display: none; cursor: pointer">
    <?= _("Hide quote") ?>
  </div>
  <div id="_bbn_quote_container"
       style="display: none">
    <?= $quote ?>
  </div>
<?php } ?>