<!-- HTML Document -->
<bbn-form :source="formSource"
          :action="root + 'webmail/actions/email/move'"
          submit-text="<?= _("Move") ?>"
          class="appui-email-webmail-mail-move"
          @success="onSuccess"
          @failure="onFailure">
  <div class="bbn-grid-fields bbn-padding">
    <label class="bbn-label"><?= _("Select the folder") ?></label>
    <bbn-dropdown :source="folders"
                  bbn-model="formSource.id_folder"/>
  </div>
</bbn-form>
