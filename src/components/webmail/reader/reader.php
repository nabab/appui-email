<div :class="['appui-email-webmail-reader', {'bbn-overlay bbn-flex-height': overlay}]">
  <div class="bbn-top-spadding bbn-hxspadding bbn-bottom-xspadding">
    <bbn-toolbar class="bbn-m bbn-no-border bbn-radius">
      <div class="bbn-flex-fill bbn-vmiddle bbn-xspadding"
          style="gap: var(--xsspace)">
        <bbn-button icon="nf nf-fa-mail_reply"
                    :label="_('Reply')"
                    :notext="true"
                    @click="reply"/>
        <bbn-button icon="nf nf-fa-mail_reply_all"
                    :label="_('Reply All')"
                    :notext="true"
                    @click="replyAll"/>
        <bbn-button icon="nf nf-fa-mail_forward"
                    :label="_('Forward')"
                    :notext="true"
                    @click="forward"/>
        <bbn-button icon="nf nf-md-tab_plus"
                    :label="_('Open in a new tab')"
                    :notext="true"
                    @click="openTab"/>
        <bbn-button icon="nf nf-md-window_restore"
                    :label="_('Open in a new window')"
                    :notext="true"
                    @click="openWindow"/>
        <bbn-button icon="nf nf-fa-archive"
                    :label="_('Archive')"
                    :notext="true"
                    @click="archive"/>
        <bbn-button icon="nf nf-weather-fire"
                    :label="_('Set as junk')"
                    :notext="true"
                    @click="setAsJunk"/>
        <bbn-button icon="nf nf-md-delete"
                    :label="_('Delete')"
                    :notext="true"
                    @click="deleteMail"/>
        <bbn-button icon="nf nf-fa-bug"
                    :label="_('Transform in task')"
                    :notext="true"
                    @click="mailToTask"/>
        <bbn-button icon="nf nf-md-folder_move"
                    :label="_('Move')"
                    :notext="true"
                    @click="moveFolder"/>
      </div>
    </bbn-toolbar>
  </div>
  <div class="bbn-vspadding bbn-hpadding">
    <div class="bbn-flex-width bbn-bottom-xsmargin">
      <div class="bbn-flex-fill">
        <span><?= _("From:") ?></span>
        <a bbn-if="source.from_name
            && source.from_email
            && (source.from_email !== source.from_name)"
          :href="'mailto:' + source.from_email"
          :title="source.from_email"
          bbn-text="source.from_name"
          class="bbn-light"/>
        <a bbn-else-if="source.from_email"
          :href="'mailto:' + source.from_email"
          bbn-text="source.from_email"
          class="bbn-light"/>
        <span bbn-else
              bbn-text="source.from"
              class="bbn-light"/>
      </div>
      <div class="bbn-small"
            bbn-text="formatDate(source.date)"/>
    </div>
    <div class="bbn-bottom-xsmargin">
      <span><?= _("To:") ?></span>
      <a bbn-if="source.to_name
          && source.to_email
          && (source.to_email !== source.to_name)"
        :href="'mailto:' + source.to_email"
        :title="source.to_email"
        bbn-text="source.to_email"
        class="bbn-light"/>
      <a bbn-else-if="source.to_email"
        :href="'mailto:' + source.to_email"
        bbn-text="source.to_email"
        class="bbn-light"/>
      <span bbn-else
            bbn-text="source.to"
            class="bbn-light"/>
    </div>
    <div class="bbn-medium"
        bbn-text="source.subject"/>
    <appui-email-webmail-reader-entities bbn-if="source.from_email"
                                         class="bbn-top-xsmargin"
                                         :identifier="source.id"
                                         :uid="source.msg_unique_id"
                                         :mailbox="source.id_account"
                                         :mail="source.from_email"/>
  </div>
  <hr class="bbn-hr">
  <div class="bbn-flex-fill bbn-hpadding bbn-vspadding">
    <div class="bbn-100">
      <bbn-frame bbn-if="source.id"
                 :url="root + 'reader/' + source.id"
                 :class="{'bbn-100': overlay, 'bbn-w-100': !overlay}"
                 :reset-style="true"
                 @load="onFrameLoaded"
                 ref="frame"/>
      <bbn-loader bbn-if="isFrameLoading"
                  class="bbn-overlay bbn-middle bbn-background"/>
    </div>
  </div>
  <div bbn-if="source.attachments?.length"
       class="bbn-top-xsmargin bbn-flex-wrap bbn-header bbn-spadding bbn-no-border bbn-radius bbn-smargin"
       style="min-height: 2.5rem; gap: var(--sspace)">
    <bbn-context bbn-if="source.attachments?.length > 1"
                 :source="attachmentsSrc"
                 source-icon="icon">
      <i class="nf nf-md-dots_vertical"/>
    </bbn-context>
    <bbn-context bbn-for="att in source.attachments"
                 :source="getAttachmentSrc(att)"
                 source-icon="icon">
      <div class="bbn-no-border bbn-radius bbn-vmiddle bbn-background bbn-xspadding bbn-reactive">
        <i :class="getFileIcon(att)"/>
        <span class="bbn-hxsmargin"
              bbn-text="att.name"/>
      </div>
    </bbn-context>
  </div>
</div>