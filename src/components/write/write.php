<!-- HTML Document -->
<div class="bbn-overlay">
  <bbn-toolbar class="bbn-w-100">
    <div>
      <bbn-dropdown v-model="currentFrom"
                    :source="accounts"/>
    </div>
    <div/>
    <div>
      <bbn-dropdown v-model="type"
                    :source="types"/>
    </div>
    <div/>
    <div>
      <bbn-dropdown v-model="currentSignature"
                    :source="signatures"
                    source-text="name"
                    source-value="id"/>
    </div>
    <div>
      <bbn-button class="bbn-button-icon-only bbn-iblock"
                  :notext="true"
                  title="<?= _('Signatures Editor') ?>"
                  icon="nf nf-fa-pencil"
                  @click="openSignatureEditor()"/>
    </div>
    <div>
      <bbn-button class="bbn-button-icon-only bbn-iblock"
                  :notext="true"
                  title="<?= _('Add signature') ?>"
                  icon="nf nf-md-sign_text"
                  @click="addSignature()"
                  :disabled="!currentSignature"/>
    </div>
  </bbn-toolbar>
  <div class="bbn-w-100 bbn-lpadding container__top">
    <div class="grid-3-top">
      <bbn-button label="<?= _('To') ?>"
                  @click="openContacts('to')"></bbn-button>
      <appui-email-multiinput :source="rootUrl + '/webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="toInput">
      </appui-email-multiinput>
      <div>
        <bbn-button label="<?= _('CC') ?>"
                    @click="ccButton = !ccButton"
                    style="height:100%"></bbn-button>
        <bbn-button
                    label="<?= _('CCI') ?>"
                    @click="cciButton = !cciButton"
                    style="height:100%"></bbn-button>
      </div>
      <bbn-button style="grid-column-start: 1;"
                  v-if="ccButton"
                  label="<?= _('CC') ?>"
                  @click="openContacts('cc')"></bbn-button>
      <appui-email-multiinput v-if="ccButton"
                              :source="rootUrl + '/webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="ccInput"
                              style="grid-column-start: 2; grid-column-end: 4;">
      </appui-email-multiinput>
      <bbn-button style="grid-column-start: 1;"
                  v-if="cciButton"
                  label="<?= _('CCI') ?>"
                  @click="openContacts('cci')"></bbn-button>
      <appui-email-multiinput v-if="cciButton"
                              :source="rootUrl + '/webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="cciInput"
                              style="grid-column-start: 2; grid-column-end: 4;">
      </appui-email-multiinput>
      <div class="span-text-center"
           style="grid-column-start: 1;">
        <span class="bbn-m">{{trlt.subject}}</span>
      </div>
      <bbn-input class="bbn-m"
                 v-model="currentSubject"
                 style="grid-column-start: 2; grid-column-end: 4;">
      </bbn-input>
      <bbn-upload v-model="attachmentsModel"
                  style="grid-column-start: 1; grid-column-end: 3;"
                  :save-url="rootUrl + '/actions/email/upload_file'"
                  @success="uploadSuccess"></bbn-upload>
      <bbn-button label="<?= _('Send') ?>"
                  @click="send"
                  icon="nf nf-fa-send"></bbn-button>
    </div>
    <component :is="type"
               v-model="message"
               style="width: 100%; min-height: 40vh"></component>
  </div>
</div>