<bbn-form :source="source"
          @success="success"
          :data="{action: 'save'}"
          :action="root + 'webmail/actions/account'"
          ref="form"
          :buttons="formButtons"
          class="appui-email-webmail-account"
          autocomplete="off"
          @submit="ev => currentPage === 1 ? nextToTest(ev) : false">
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
                   :required="true"/>
        <div class="bbn-label"><?= _("Outgoing server") ?></div>
        <bbn-input bbn-model="source.smtp"
                   type="hostname"
                   :required="true"/>
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
    <bbn-splitter bbn-elseif="tree.length"
                  :full-size="false">
      <bbn-pane>
        <div class="bbn-m bbn-c bbn-secondary bbn-radius bbn-spadding">
          <?= _("Choose the folders you want to keep synchronized") ?>
        </div>
        <div class="bbn-padding">
          <div @click="checkUncheckAll"
               class="bbn-p bbn-bottom-sspace bbn-hxspadding">
            <i bbn-if="isAllChecked"
               class="nf nf-md-checkbox_outline bbn-m"/>
            <i bbn-elseif="isIntermediateChecked"
               class="nf nf-md-checkbox_intermediate bbn-m"/>
            <i bbn-else
               class="nf nf-md-checkbox_blank_outline bbn-m"/>
            <span bbn-if="isAllChecked"><?=_("Uncheck all")?></span>
            <span bbn-else><?=_("Check all")?></span>
          </div>
          <bbn-tree :source="treeSource"
                    ref="tree"
                    :selection="true"
                    uid="uid"
                    :opened="true"
                    :scrollable="false"/>
        </div>
      </bbn-pane>
      <bbn-pane class="bbn-left-margin bbn-alt-background bbn-alt-text bbn-radius">
        <div class="bbn-m bbn-c bbn-secondary bbn-radius bbn-spadding">
          <?= _("Set rules for folders") ?>
        </div>
        <div class="bbn-grid-fields bbn-padding">
          <div class="bbn-label"><?= _("Inbox") ?></div>
          <bbn-dropdown/>
          <div class="bbn-label"><?= _("Drafts") ?></div>
          <bbn-dropdown/>
          <div class="bbn-label"><?= _("Sent") ?></div>
          <bbn-dropdown/>
          <div class="bbn-label"><?= _("Junk") ?></div>
          <bbn-dropdown/>
          <div class="bbn-label"><?= _("Trash") ?></div>
          <bbn-dropdown/>
          <div class="bbn-label"><?= _("Archive") ?></div>
          <bbn-dropdown/>
        </div>
      </bbn-pane>
    </bbn-splitter>
  </div>
</bbn-form>