<div :class="['appui-email-webmail-reader', {'bbn-overlay bbn-flex-height': overlay || thread}]"
     @click.stop="onSelect">
  <div bbn-if="!isInThread"
       class="bbn-top-spadding bbn-hspadding bbn-bottom-xspadding">
    <bbn-toolbar class="bbn-m bbn-no-border bbn-radius">
      <div class="bbn-flex-fill bbn-vmiddle bbn-xspadding"
          style="gap: var(--xsspace)">
        <bbn-button bbn-if="currentSelectedSource?.is_draft"
                    icon="nf nf-md-text_box_edit"
                    :label="_('Edit')"
                    :notext="true"
                    @click="edit"
                    :disabled="!currentSelectedSource"/>
        <bbn-button bbn-if="!currentSelectedSource?.is_draft"
                    icon="nf nf-fa-mail_reply"
                    :label="_('Reply')"
                    :notext="true"
                    @click="reply"
                    :disabled="!currentSelectedSource || !!currentSelectedSource.is_draft"/>
        <bbn-button bbn-if="!currentSelectedSource?.is_draft"
              icon="nf nf-fa-mail_reply_all"
                    :label="_('Reply All')"
                    :notext="true"
                    @click="replyAll"
                    :disabled="!currentSelectedSource || !!currentSelectedSource.is_draft"/>
        <bbn-button icon="nf nf-fa-mail_forward"
                    :label="_('Forward')"
                    :notext="true"
                    @click="forward"
                    :disabled="!currentSelected"/>
        <bbn-button icon="nf nf-md-tab_plus"
                    :label="_('Open in a new tab')"
                    :notext="true"
                    @click="openTab"
                    :disabled="!currentSelected"/>
        <bbn-button icon="nf nf-md-window_restore"
                    :label="_('Open in a new window')"
                    :notext="true"
                    @click="openWindow"
                    :disabled="!currentSelected"/>
        <bbn-button bbn-if="archiveFolderId && !currentSelectedSource?.is_draft"
                    icon="nf nf-fa-archive"
                    :label="_('Archive')"
                    :notext="true"
                    @click="archive"
                    :disabled="!currentSelectedSource || !!currentSelectedSource.is_draft"/>
        <bbn-button bbn-if="spamFolderId && !currentSelectedSource?.is_draft"
                    icon="nf nf-weather-fire"
                    :label="_('Mark as spam')"
                    :notext="true"
                    @click="moveToSpam"
                    :disabled="!currentSelectedSource || !!currentSelectedSource.is_draft"/>
        <bbn-button icon="nf nf-md-delete"
                    :label="_('Delete')"
                    :notext="true"
                    @click="deleteMail"
                    :disabled="!currentSelected"/>
        <bbn-button bbn-if="otherFolders?.length && !currentSelectedSource?.is_draft"
                    icon="nf nf-md-folder_move"
                    :label="_('Move')"
                    :notext="true"
                    @click="moveFolder"
                    :disabled="!currentSelectedSource || !!currentSelectedSource.is_draft"/>
        <component bbn-if="webmail?.pluginsSlots?.reader?.toolbar?.length"
                   bbn-for="(slot, idx) in webmail.pluginsSlots.reader.toolbar"
                   :key="'toolbar-' + idx"
                   :is="slot.cp"
                   :source="slot.data"/>
      </div>
    </bbn-toolbar>
  </div>
  <bbn-kanban-element bbn-if="thread"
                      class="appui-email-webmail-thread bbn-noradius bbn-flex-fill"
                      :source="source.thread"
                      component="appui-email-webmail-reader"
                      :pageable="false"
                      :filterable="false"
                      :selection="true"
                      ref="thread"
                      :sortable="true"
                      :order="[{field: 'date', dir: 'DESC'}]"/>
  <template bbn-else>
    <div bbn-if="isInThread && index"
         class="bbn-w-100 bbn-header bbn-no-border bbn-xspadding bbn-radius bbn-top-space bbn-bottom-space"
         @click.stop.prevent/>
    <div :class="['bbn-padding', 'bbn-radius', 'bbn-no-border', {
           'bbn-header': !isSelected,
           'bbn-background-secondary bbn-secondary-text': isSelected,
           'bbn-noradius-bottom': isSelected,
           'bbn-hsmargin': !isInThread
         }]">
      <div class="bbn-flex-width bbn-bottom-xsmargin">
        <div class="bbn-flex-fill bbn-blue">
          <a bbn-if="source.from_name
              && source.from_email
              && (source.from_email !== source.from_name)"
            :href="'mailto:' + source.from_email"
            :title="source.from_email"
            bbn-text="source.from_name"/>
          <a bbn-else-if="source.from_email"
            :href="'mailto:' + source.from_email"
            bbn-text="source.from_email"/>
          <span bbn-else
                bbn-text="source.from"/>
        </div>
        <div class="bbn-small"
              bbn-text="formatDate(source.date)"/>
      </div>
      <div class="bbn-flex-width bbn-bottom-xsmargin">
        <div class="bbn-flex-fill bbn-flex-width">
          <span class="bbn-right-xsspace"><?= _("To:") ?></span>
          <div class="bbn-flex-fill bbn-flex-wrap bbn-grid-xsgap">
            <span class="bbn-radius bbn-background bbn-text bbn-hxspadding"
                  bbn-for="(r, i) in recipients">
              <a bbn-if="recipientsNames[i]
                  && recipientsEmails[i]
                  && (recipientsEmails[i] !== recipientsNames[i])"
                :href="'mailto:' + recipientsEmails[i]"
                :title="recipientsEmails[i]"
                bbn-text="recipientsNames[i]"/>
              <a bbn-else-if="recipientsEmails[i]"
                :href="'mailto:' + recipientsEmails[i]"
                bbn-text="recipientsEmails[i]"/>
              <span bbn-else
                    bbn-text="r"/>
            </span>
          </div>
        </div>
        <bbn-context bbn-if="source.attachments?.length"
                     :source="attachmentsSrc"
                     source-icon="icon"
                     class="bbn-vmiddle">
          <i class="nf nf-md-paperclip bbn-m"
             style="align-self: center"/>
          <span bbn-text="source.attachments.length"/>
        </bbn-context>
      </div>
      <div class="bbn-bottom-xsmargin"
           bbn-text="source.subject"/>
      <appui-email-webmail-reader-entities bbn-if="source.from_email"
                                           :identifier="source.id"
                                           :uid="source.msg_unique_id"
                                           :mailbox="source.id_account"
                                           :mail="source.from_email"/>
    </div>
    <div :class="['bbn-flex-fill', 'bbn-padding', {'bbn-secondary-border bbn-radius-bottom': isSelected}]">
      <div class="bbn-100">
        <bbn-frame bbn-if="source.id"
                    :url="root + 'reader/' + source.id"
                    :class="{'bbn-100': overlay, 'bbn-w-100': !overlay}"
                    :reset-style="true"
                    @load="onFrameLoaded"
                    ref="frame"
                    @click="onSelect"/>
        <bbn-loader bbn-if="isFrameLoading"
                    class="bbn-overlay bbn-middle bbn-background"/>
      </div>
    </div>
  </template>
</div>