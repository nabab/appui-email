<bbn-form :source="source"
          @success="success"
          :data="{action: 'save'}"
          :action="root + 'webmail/actions/account'"
          ref="form"
          :buttons="formButtons"
          class="appui-email-webmail-account"
          autocomplete="off">
  <div bbn-if="currentPage === 1"
       class="bbn-w-100">
    <div class="bbn-grid-fields bbn-padding bbn-m">
      <div class="bbn-label"><?= _("Account type") ?></div>
      <bbn-dropdown :source="types"
                    source-value="id"
                    placeholder="<?= _("Choose a type of account") ?>"
                    bbn-model="source.type"
                    :required="true"/>
      <div class="bbn-label"><?= _("eMail address") ?></div>
      <bbn-input bbn-model="source.email"
                type="email"
                :required="true"/>
      <div class="bbn-label"><?= _("Login") ?></div>
      <bbn-input bbn-model="source.login"
                :required="true"/>
      <div class="bbn-label"><?= _("Password") ?></div>
      <bbn-input bbn-model="source.pass"
                type="password"
                :no-save="true"
                :required="true"/>
      <div bbn-if="isDev"
           class="bbn-label"><?= _("Private account") ?></div>
      <bbn-switch bbn-if="isDev"
                  bbn-model="source.locale"
                  :value="true"
                  :novalue="false"/>
      <template bbn-if="['imap', 'pop3'].includes(accountCode)">
        <div class="bbn-label"><?= _("Use SSL") ?></div>
        <bbn-checkbox :value="1"
                      :novalue="0"
                      bbn-model="source.ssl"/>
        <div class="bbn-label"><?= _("Incoming server") ?></div>
        <bbn-input type="hostname"
                   bbn-model="source.host"
                   :required="true"
                   @change="inServerChanged = !!source.host.length"/>
        <div class="bbn-label"><?= _("Outgoing server") ?></div>
        <bbn-input bbn-model="source.smtp"
                   type="hostname"
                   :required="true"
                   @change="outServerChanged = !!source.smtp.length"/>
      </template>
    </div>
  </div>

  <div bbn-elseif="currentPage === 2"
       class="bbn-w-100 bbn-padding">
    <bbn-loader bbn-if="isTesting"
                label="<?=_('Testing account...')?>"
                style="position: relative !important"/>
    <div bbn-elseif="errorState"
         class="bbn-c bbn-b bbn-state-error bbn-padding">
      <?= _("Impossible to connect to the mail server") ?>
    </div>
    <div bbn-elseif="tree.length">
      <div class="bbn-m bbn-b bbn-c">
        <?= _("Choose the folders you want to keep synchronized") ?>
      </div>
      <div class="bbn-padding">
        <bbn-tree :source="tree"
                  ref="tree"
                  :selection="true"
                  uid="uid"
                  :opened="true"
                  :scrollable="false"/>
      </div>
    </div>
  </div>
</bbn-form>