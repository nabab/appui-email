<!-- HTML Document -->
<div class="bbn-overlay">
  <bbn-toolbar class="bbn-no-border bbn-radius bbn-smargin bbn-spadding">
    <bbn-button label="<?= _('Send') ?>"
                @click="send"
                icon="nf nf-fa-send"/>
    <bbn-button label="<?= _('Save draft') ?>"
                @click="saveDraft"
                icon="nf nf-fa-save"/>
    <div/>
    <div>
      <bbn-dropdown bbn-model="currentAccount"
                    :source="accounts"/>
    </div>
    <div/>
    <div>
      <bbn-dropdown bbn-model="type"
                    :source="types"/>
    </div>
    <div/>
    <div>
      <bbn-dropdown bbn-model="currentSignature"
                    :source="signatures"
                    source-text="name"
                    source-value="id"/>
    </div>
    <div>
      <bbn-button class="bbn-iblock"
                  :notext="true"
                  title="<?= _('Signatures Editor') ?>"
                  icon="nf nf-fa-pencil"
                  @click="openSignatureEditor()"/>
    </div>
    <div>
      <bbn-button class="bbn-iblock"
                  :notext="true"
                  title="<?= _('Add signature') ?>"
                  icon="nf nf-md-sign_text"
                  @click="addSignature()"
                  :disabled="!currentSignature"/>
    </div>
  </bbn-toolbar>
  <div class="bbn-hlpadding bbn-vpadding container__top">
    <div class="grid-3-top">
      <bbn-button label="<?= _('To') ?>"
                  @click="openContacts('to')"
                  style="align-self: start"/>
      <appui-email-multiinput :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="toInput"
                              bbn-model="currentTo"/>
      <div style="align-self: start">
        <bbn-button label="<?= _('CC') ?>"
                    @click="ccButton = !ccButton"
                    :class="{'bbn-state-active': !!ccButton}"/>
        <bbn-button label="<?= _('CCI') ?>"
                    @click="cciButton = !cciButton"
                    :class="{'bbn-state-active': !!cciButton}"/>
      </div>
      <bbn-button style="grid-column-start: 1; align-self: start"
                  bbn-if="ccButton"
                  label="<?= _('CC') ?>"
                  @click="openContacts('cc')"/>
      <appui-email-multiinput bbn-if="ccButton"
                              :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="ccInput"
                              style="grid-column-start: 2; grid-column-end: 4;"
                              bbn-model="currentCC"/>
      <bbn-button style="grid-column-start: 1;"
                  bbn-if="cciButton"
                  label="<?= _('CCI') ?>"
                  @click="openContacts('cci')"/>
      <appui-email-multiinput bbn-if="cciButton"
                              :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="cciInput"
                              style="grid-column-start: 2; grid-column-end: 4;"
                              bbn-model="currentCCI"/>
      <div class="span-text-center"
           style="grid-column-start: 1;">
        <span class="bbn-m"><?=_('Subject')?></span>
      </div>
      <bbn-input class="bbn-m"
                 bbn-model="currentSubject"
                 style="grid-column-start: 2; grid-column-end: 4;"/>
      <div class="bbn-flex-width bbn-vmiddle"
           style="grid-column-start: 1; grid-column-end: 4">
        <span class="bbn-m"><?=_('Attachment')?></span>
        <bbn-upload bbn-model="attachmentsModel"
                    :save-url="rootUrl + 'webmail/actions/attachment/upload/save'"
                    :remove-url="rootUrl + 'webmail/actions/attachment/upload/remove'"
                    class="bbn-flex-fill bbn-left-space"
                    :data="{
                      timestamp: timestamp
                    }"/>
      </div>
    </div>
    <bbn-rte bbn-if="type === 'bbn-rte'"
             bbn-model="message"
             style="width: 100%; min-height: 40rem; height: 40rem"/>
    <bbn-markdown bbn-elseif="type === 'bbn-markdown'"
             bbn-model="message"
             style="width: 100%; height: 40rem"/>
    <bbn-textarea bbn-elseif="type === 'bbn-textarea'"
             bbn-model="message"
             style="width: 100%; height: 40rem"/>
  </div>
</div>