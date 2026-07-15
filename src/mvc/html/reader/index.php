<?= $html ?: nl2br($plain) ?>
<?php if (!empty($quote)) { ?>
  <div id="_bbn_show_quote"
       style="cursor: pointer; margin-top: 0.5rem; font-style: italic; font-size: 0.8rem; font-variant: all-small-caps">
    <?= _("Show quote") ?>
  </div>
  <div id="_bbn_hide_quote"
       style="display: none; cursor: pointer; margin-top: 0.5rem; font-style: italic; font-size: 0.8rem; font-variant: all-small-caps">
    <?= _("Hide quote") ?>
  </div>
  <div id="_bbn_quote_container"
       style="display: none">
    <?= $quote ?>
  </div>
<?php } ?>