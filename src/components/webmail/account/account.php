<bbn-form :source="source"
          @success="success"
          :data="{
            action: source.id ? 'update' : 'insert'
          }"
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
      <div bbn-if="isDev && !source.id"
           class="bbn-label"><?= _("Private account") ?></div>
      <bbn-switch bbn-if="isDev && !source.id"
                  bbn-model="source.locale"
                  :value="true"
                  :novalue="false"/>
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
      <template bbn-if="typeCode === 'imap'">
        <div class="bbn-label"><?= _("Incoming server") ?></div>
        <bbn-input type="hostname"
                   bbn-model="source.host"
                   :required="true"/>
        <div class="bbn-label"><?= _("Use encryption") ?></div>
        <bbn-switch :value="1"
                    :novalue="0"
                    bbn-model="source.encryption"/>
        <div class="bbn-label"><?= _("Port") ?></div>
        <bbn-input type="number"
                   bbn-model="source.port"
                   :required="true"/>
        <div bbn-if="!!source.encryption"
             class="bbn-label"><?= _("Validate certificate") ?></div>
        <bbn-switch bbn-if="!!source.encryption"
                    :value="1"
                    :novalue="0"
                    bbn-model="source.validatecert"/>
        <div class="bbn-label"><?= _("Outgoing server") ?></div>
        <div>
          <bbn-dropdown :source="smtps"
                        bbn-model="source.smtp"
                        :required="true"
                        source-value="id"
                        source-text="name"/>
          <bbn-button bbn-if="!!source.smtp"
                      icon="nf nf-fa-edit"
                      :notext="true"
                      @click="editSmtp(source.smtp)"/>
          <bbn-button icon="nf nf-fa-plus"
                      :notext="true"
                      @click="addSmtp"/>
        </div>
      </template>
    </div>
  </div>

  <div bbn-elseif="currentPage === 2"
       class="bbn-w-100 bbn-padding">
    <bbn-loader bbn-if="isTesting"
                label="<?=_('Testing account...')?>"
                style="position: relative !important"/>
    <div bbn-elseif="errorState"
         class="bbn-c bbn-padding bbn-state-error bbn-radius">
      <div class="bbn-b"><?= _("Impossible to connect to the mail server") ?></div>
      <div bbn-if="errorMessage"
           bbn-text="errorMessage"
           class="bbn-text"/>
    </div>
    <bbn-splitter bbn-elseif="tree.length"
                  :full-size="false"
                  orientation="horizontal">
      <bbn-pane>
        <div class="bbn-m bbn-c bbn-background-secondary bbn-secondary-text bbn-radius-top bbn-spadding bbn-upper"
             style="max-width: 25rem">
          <?= _("Choose the folders you want to keep synchronized") ?>
        </div>
        <div class="bbn-padding bbn-radius-bottom bbn-border-bottom bbn-border-left bbn-border-right"
             style="border-color: var(--secondary-background) !important">
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
                    :scrollable="false"
                    @check="onTreeCheck"
                    @uncheck="onTreeUncheck"/>
        </div>
      </bbn-pane>
      <bbn-pane bbn-if="isTreeLoaded"
                class="bbn-left-margin bbn-radius">
        <div class="bbn-m bbn-c bbn-background-tertiary bbn-tertiary-text bbn-radius-top bbn-spadding bbn-upper">
          <?= _("Mailbox rules") ?>
        </div>
        <div class="bbn-grid-fields bbn-padding bbn-radius-bottom bbn-border-bottom bbn-border-left bbn-border-right"
             style="border-color: var(--tertiary-background) !important">
          <div class="bbn-label"><?= _("Inbox folder") ?></div>
          <bbn-dropdown :source="availableFolders('inbox')"
                        bbn-model="source.rules.inbox"
                        :required="true"/>
          <div class="bbn-label"><?= _("Drafts folder") ?></div>
          <bbn-dropdown :source="availableFolders('drafts')"
                        bbn-model="source.rules.drafts"
                        :required="true"/>
          <div class="bbn-label"><?= _("Sent folder") ?></div>
          <bbn-dropdown :source="availableFolders('sent')"
                        bbn-model="source.rules.sent"
                        :required="true"/>
          <div class="bbn-label"><?= _("Spam folder") ?></div>
          <bbn-dropdown :source="availableFolders('spam')"
                        bbn-model="source.rules.spam"
                        :required="true"/>
          <div class="bbn-label"><?= _("Trash folder") ?></div>
          <bbn-dropdown :source="availableFolders('trash')"
                        bbn-model="source.rules.trash"
                        :required="true"/>
          <div class="bbn-label"><?= _("Archive folder") ?></div>
          <bbn-dropdown :source="availableFolders('archive')"
                        bbn-model="source.rules.archive"
                        :required="true"/>
        </div>
      </bbn-pane>
    </bbn-splitter>
  </div>
</bbn-form>