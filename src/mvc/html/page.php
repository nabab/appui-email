<div class="appui-email bbn-overlay">
  <bbn-router class="appui_email_nav"
              :scrollable="true"
              :autoload="true"
              :nav="true">
    <bbns-container url="home"
                    :load="true"
                    label="<?= _('Mailings') ?>"
                    icon="nf nf-fa-newspaper"
                    :fixed="true"/>
  </bbn-router>
</div>