<!-- HTML Document -->
<div class="appui-email-webmail-write bbn-overlay bbn-flex-height">
  <bbn-toolbar class="appui-email-webmail-write-toolbar bbn-no-border bbn-radius bbn-smargin bbn-spadding">
    <bbn-button label="<?= _('Send') ?>"
                @click="send"
                icon="nf nf-fa-send"
                :notext="true"
                style="border-radius: 2rem; height: 3rem"
                class="bbn-lg bbn-primary"/>
    <bbn-button label="<?= _('Save draft') ?>"
                @click="saveDraft"
                icon="nf nf-fa-save"
                :notext="true"
                style="height: 2.3rem"
                class="bbn-tertiary bbn-m"/>
    <div/>
    <div class="bbn-flex"
         title="<?= _('Priority') ?>">
      <span class="bbn-leftlabel">
        <i class="nf nf-fa-exclamation bbn-m"/>
      </span>
      <bbn-dropdown bbn-model="currentPriority"
                    :source="priorityList"
                    placeholder="<?= _("Priority") ?>"
                    class="bbn-narrow"
                    :clear-html="true"/>
    </div>
    <div/>
    <div class="bbn-flex"
         title="<?= _('Signature') ?>">
      <span class="bbn-leftlabel">
        <i class="nf nf-fa-signature bbn-lg"/>
      </span>
      <bbn-dropdown bbn-model="currentSignature"
                    :source="signatures"
                    source-text="name"
                    source-value="id"
                    ref="signatures"
                    placeholder="<?= _("Signature") ?>"
                    :nullable="true"/>
    </div>
    <div>
      <bbn-button class="bbn-iblock"
                  :notext="true"
                  title="<?= _('Signatures Editor') ?>"
                  icon="nf nf-fa-pencil"
                  @click="openSignatureEditor()"/>
    </div>
    <div bbn-if="isReply"/>
    <div bbn-if="isReply"
         class="bbn-vmiddle"
         style="align-items: stretch"
         title="<?= _('Include quote') ?>">
      <span class="bbn-leftlabel">
        <i class="nf nf-cod-quote bbn-m"/>
      </span>
      <span class="bbn-border bbn-radius bbn-vmiddle appui-email-webmail-write-quoteswitch">
        <bbn-switch bbn-model="includeQuote"
                    :value="true"
                    :novalue="false"/>
      </span>
    </div>
    <div bbn-if="ai"/>
    <div bbn-if="ai"
         class="bbn-vmiddle"
         style="align-items: stretch">
      <span class="bbn-leftlabel"
            title="<?= _('AI') ?>"
            style="border-color: var(--secondary-background) !important; background-color: var(--secondary-background) !important; color: var(--secondary-text) !important">
        <i class="nf nf-md-robot_outline bbn-lg"/>
      </span>
      <span class="bbn-border bbn-radius bbn-vmiddle"
            style="border-color: var(--secondary-background) !important">
        <bbn-button label="<?= _('AI Correct') ?>"
                    @click="aiCorrect"
                    icon="nf nf-md-auto_fix"
                    :notext="true"
                    class="bbn-noborder"
                    style="border-radius: 0"/>
        <bbn-context :source="aiRewriteSource"
                     :context="false"
                     ref="aiRewriteContext">
          <bbn-button label="<?= _('AI Rewrite') ?>"
                      icon="nf nf-md-refresh_auto"
                      @click="$refs.aiRewriteContext.click()"
                      :notext="true"
                      :class="['bbn-noborder', {
                        'appui-email-webmail-write-ailastbutton': !entities?.length
                      }]"
                      :style="entities?.length ? {borderRadius: '0'} : {}"/>
        </bbn-context>
        <bbn-context :source="aiEntityReplySource"
                     :context="false"
                     ref="aiEntityReplyContext">
          <bbn-button bbn-if="entities?.length"
                      label="<?= _('AI Reply') ?>"
                      icon="nf nf-md-comment_processing_outline"
                      :notext="true"
                      class="bbn-noborder appui-email-webmail-write-ailastbutton"
                      @click="$refs.aiEntityReplyContext.click()"/>
        </bbn-context>
      </span>
    </div>
  </bbn-toolbar>
  <div class="bbn-spadding bbn-flex-fill bbn-flex-height">
    <div class="bbn-bottom-margin">
      <div class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <div><?= _('From:') ?></div>
        <bbn-dropdown bbn-model="currentAccount"
                      :source="accounts"
                      class="bbn-flex-fill bbn-no-border"/>
      </div>
      <div class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <div><?= _('To:') ?></div>
        <i class="nf nf-fa-address_book bbn-p bbn-lg"
           @click="openContacts('to')"
           style="line-height: normal"/>
        <appui-email-multiinput :source="rootUrl + 'webmail/contacts'"
                                source-text="displayName"
                                source-value="id"
                                ref="toInput"
                                bbn-model="currentTo"
                                class="bbn-flex-fill bbn-no-border"/>
        <div>
          <bbn-button label="<?= _('Cc') ?>"
                      @click="ccButton = !ccButton"
                      :class="{'bbn-state-active': !!ccButton}"/>
          <bbn-button label="<?= _('Bcc') ?>"
                      @click="bccButton = !bccButton"
                      :class="{'bbn-state-active': !!bccButton}"/>
        </div>
      </div>
      <div bbn-if="ccButton"
           class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <div><?= _('Cc:') ?></div>
        <i class="nf nf-fa-address_book bbn-p bbn-lg"
           @click="openContacts('cc')"
           style="line-height: normal"/>
        <appui-email-multiinput bbn-if="ccButton"
                                :source="rootUrl + 'webmail/contacts'"
                                source-text="displayName"
                                source-value="id"
                                ref="ccInput"
                                bbn-model="currentCc"
                                class="bbn-flex-fill bbn-no-border"/>
      </div>
      <div bbn-if="bccButton"
           class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <div><?= _('Bcc:') ?></div>
        <i class="nf nf-fa-address_book bbn-p bbn-lg"
           @click="openContacts('bcc')"
           style="line-height: normal"/>
        <appui-email-multiinput bbn-if="bccButton"
                                :source="rootUrl + 'webmail/contacts'"
                                source-text="displayName"
                                source-value="id"
                                ref="bccInput"
                                bbn-model="currentBcc"
                                class="bbn-flex-fill bbn-no-border"/>
      </div>
      <div class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <div><?= _('Subject:') ?></div>
        <bbn-input bbn-model="currentSubject"
                   class="bbn-flex-fill bbn-no-border"/>
      </div>
      <div class="appui-email-webmail-write-section bbn-border-bottom bbn-flex-width bbn-hxspadding bbn-vspadding">
        <span><?=_('Attachment')?></span>
        <bbn-upload bbn-model="attachmentsModel"
                    :save-url="rootUrl + 'webmail/actions/attachment/upload/save'"
                    :remove-url="rootUrl + 'webmail/actions/attachment/upload/remove'"
                    class="bbn-flex-fill bbn-no-border"
                    :data="{
                      timestamp: timestamp
                    }"/>
      </div>
    </div>
    <div bbn-if="false" class="appui-email-webmail-write-header">
      <div style="grid-column-start: 1;">
        <?= _('From') ?>
      </div>
      <bbn-dropdown bbn-model="currentAccount"
                    :source="accounts"
                    style="grid-column-start: 2; grid-column-end: 4; align-self: stretch"/>
      <bbn-button label="<?= _('To') ?>"
                  @click="openContacts('to')"
                  style="align-self: start"/>
      <appui-email-multiinput :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="toInput"
                              bbn-model="currentTo"/>
      <div style="align-self: start">
        <bbn-button label="<?= _('Cc') ?>"
                    @click="ccButton = !ccButton"
                    :class="{'bbn-state-active': !!ccButton}"/>
        <bbn-button label="<?= _('Bcc') ?>"
                    @click="bccButton = !bccButton"
                    :class="{'bbn-state-active': !!bccButton}"/>
      </div>
      <bbn-button style="grid-column-start: 1; align-self: start"
                  bbn-if="ccButton"
                  label="<?= _('Cc') ?>"
                  @click="openContacts('cc')"/>
      <appui-email-multiinput bbn-if="ccButton"
                              :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="ccInput"
                              style="grid-column-start: 2; grid-column-end: 4;"
                              bbn-model="currentCc"/>
      <bbn-button style="grid-column-start: 1;"
                  bbn-if="bccButton"
                  label="<?= _('Bcc') ?>"
                  @click="openContacts('bcc')"/>
      <appui-email-multiinput bbn-if="bccButton"
                              :source="rootUrl + 'webmail/contacts'"
                              source-text="displayName"
                              source-value="id"
                              ref="bccInput"
                              style="grid-column-start: 2; grid-column-end: 4;"
                              bbn-model="currentBcc"/>
      <div class="bbn-vmiddle"
           style="grid-column-start: 1;">
        <?=_('Subject')?>
      </div>
      <bbn-input bbn-model="currentSubject"
                 style="grid-column-start: 2; grid-column-end: 4;"/>
      <div class="bbn-flex-width bbn-vmiddle"
           style="grid-column-start: 1; grid-column-end: 4">
        <span><?=_('Attachment')?></span>
        <bbn-upload bbn-model="attachmentsModel"
                    :save-url="rootUrl + 'webmail/actions/attachment/upload/save'"
                    :remove-url="rootUrl + 'webmail/actions/attachment/upload/remove'"
                    class="bbn-flex-fill bbn-left-space"
                    :data="{
                      timestamp: timestamp
                    }"/>
      </div>
    </div>
    <div class="bbn-flex-fill"
         style="min-height: 40rem;">
      <bbn-rte bbn-model="message"
               style="width: 100%; height: 100%"
               ref="editor"
               height="100%"
               class="appui-email-webmail-write-editor bbn-no-border"/>
    </div>
  </div>
</div>