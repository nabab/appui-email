<bbn-form :source="formData"
          :action="root + 'webmail/actions/folder/rename'"
          class="appui-email-webmail-folder-rename"
          @success="success"
          @failure="failure"
          @submit="onSubmit">
  <div class="bbn-grid-fields bbn-padding">
    <div class="bbn-label"><?=_("Current name")?></div>
    <div bbn-text="name"/>
    <div class="bbn-label"><?=_("New name")?></div>
    <bbn-input bbn-model="formData.name"
               class="bbn-wide"
               :required="true"/>
  </div>
</bbn-form>