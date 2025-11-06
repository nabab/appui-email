<bbn-form :source="formData"
          :action="root + 'webmail/actions/folder/create'"
          class="appui-email-webmail-folder-create"
          @success="success"
          @failure="failure">
  <div class="bbn-grid-fields bbn-padding">
    <div class="bbn-label"><?= _("Folder name") ?></div>
    <bbn-input bbn-model="formData.name"
               :required="true"
               autofocus="true"/>
  </div>
</bbn-form>