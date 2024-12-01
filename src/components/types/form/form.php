<bbn-form :source="source"
          ref="form"
          @success="success"
          :action="action">
  <appui-note-toolbar-version bbn-if="source.hasVersions"
                              :source="source"
                              :data="{id: source.id_note}"
                              @version="getVersion"
                              :actionUrl="root + '/data/version'"/>
  <div class="bbn-padding bbn-grid-fields">
    <label bbn-if="emptyCategories && emptyCategories.length"><?= _('Type') ?></label>
    <bbn-dropdown bbn-if="emptyCategories && emptyCategories.length"
                  bbn-model="source.id_type"
                  :source="emptyCategories"
                  source-value="id"
                  :nullable="false"/>
    <label><?= _('Name') ?></label>
    <bbn-input bbn-model="source.name"/>
    <label><?= _('Object') ?></label>
    <bbn-input bbn-model="source.title"/>
    <label><?= _('Text') ?></label>
    <div style="height: 400px;">
      <div class="bbn-h-100">
        <bbn-rte bbn-model="source.content"
                 ref="editor"/>
      </div>
    </div>
  </div>
</bbn-form>