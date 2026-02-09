<bbn-form bbn-if="dataToSend && emails"
          :source="source.row"
          :data="dataToSend"
          ref="form"
          confirm-leave="<?= _("Are you sure you want to leave this form without saving your changes?") ?>"
          :action="emails.source.root + 'actions/' + (source.row.id ? 'update' : 'insert')"
          :prefilled="prefilled"
          @success="success"
          @failure="failure"
          :disabled="isNumLoading"
          @cancel="onFormCancel">
  <appui-note-toolbar-version bbn-show="source.row.hasVersions"
                              :source="source.row"
                              :data="{id: source.row.id_note}"
                              @version="setVersionContent"
                              :actionUrl="root + 'data/mailing_version'"
                              ref="version"
                              @hook:mounted="getRef('version').getVersion(source.row.version)"/>
  <div class="bbn-padding bbn-grid-fields">
    <div bbn-if="emails.source.senders.length === 2"
         class="bbn-grid-full bbn-middle">
      <span bbn-text="emails.source.senders[0].text"
            :title="emails.source.senders[0].desc"
            class="bbn-iblock bbn-h-100 bbn-spadding bbn-m bbn-b"/>
      <bbn-switch :value="emails.source.senders[1].value"
                  :novalue="emails.source.senders[0].value"
                  bbn-model="source.row.sender"/>
      <span bbn-text="emails.source.senders[1].text"
            :title="emails.source.senders[1].desc"
            class="bbn-iblock bbn-h-100 bbn-spadding bbn-m bbn-b"/>
    </div>

    <label bbn-if="emails.source.senders.length > 2"><?= _("Sender email") ?></label>
    <bbn-dropdown bbn-if="emails.source.senders.length > 2"
                  placeholder="<?= _("Choose") ?>"
                  bbn-model="source.row.sender"
                  :source="emails.source.senders"
                  :required="true"/>

    <label><?= _("Recipients") ?></label>
    <div class="bbn-vmiddle">
      <bbn-dropdown placeholder="<?= _("Choose") ?>"
                    bbn-model="source.row.recipients"
                    :source="emails.source.recipients"
                    :required="true"
                    class="bbn-wide"/>
      <div bbn-if="source.row.recipients"
           class="bbn-iblock bbn-m bbn-left-space">
        <span bbn-if="isNumLoading"
              class="bbn-anim-blink">
          <?= _("Retrieving list...") ?>
        </span>
        <span bbn-else
              class="bbn-i">
          <span bbn-text="numRecipients"/> <?= _('recipients') ?>
        </span>
      </div>
    </div>

    <label><?= _("Sending time") ?></label>
    <div>
      <bbn-datetimepicker bbn-model="source.row.sent"
                          :min="today"
                          class="bbn-wide"
                          :autosize="false"
                          value-format="YYYY-MM-DD HH:mm:00"/>
    </div>

    <label bbn-if="source.row.sent"><?= _("Priority") ?></label>
    <div bbn-if="source.row.sent"
         class="bbn-vmiddle">
      <span bbn-text="_('Normal')"
            class="bbn-iblock bbn-hmargin"/>
      <bbn-switch bbn-model="source.row.priority"
                  :value="4"
                  :novalue="5"/>
      <span bbn-text="_('High')"
            class="bbn-iblock bbn-hmargin"/>
    </div>

    <!--<label><?= _("Letter type") ?></label>
    <bbn-dropdown placeholder="<?= _("Choose") ?>"
                  bbn-model="source.row.lettre_type"
                  :source="emails.source.types"
                  source-value="id"
                  @change="loadLettre"/>-->

    <label><?= _("Object") ?></label>
    <bbn-input required="required"
               bbn-model="source.row.title"
               maxlength="128"/>

    <label><?= _("Attachments") ?></label>
    <bbn-upload :save-url="'file/save/' + ref"
                :multiple="true"
                bbn-model="source.row.attachments"
                :paste="true"/>

    <label><?= _("Text") ?></label>
    <bbn-rte bbn-model="source.row.content"
             :required="true"
             :clean-paste="true"
             ref="editor"
             :height="400"/>

  </div>
</bbn-form>
