<div class="appui-email-webmail-write-signatures bbn-overlay bbn-flex-height">
  <bbn-toolbar class="bbn-xspadding"
               :disabled="isSaving">
    <bbn-dropdown bbn-model="currentSignature"
                  :source="signatures"
                  :disabled="!isSaved"
                  ref="signDropdown"
                  source-value="id"
                  source-text="name"/>
    <div/>
    <bbn-button title="<?= _('New') ?>"
                icon="nf nf-md-plus"
                :notext="true"
                @click="newSign"
                :disabled="isNew && !currentText && !currentName"/>
    <bbn-button title="<?= _('Delete') ?>"
                icon="nf nf-fa-trash"
                :notext="true"
                @click="deleteSign"
                :disabled="isNew"/>
    <bbn-button title="<?= _('Save') ?>"
                icon="nf nf-md-content_save"
                :notext="true"
                @click="saveSign"
                :disabled="isSaved"/>
  </bbn-toolbar>
  <div class="bbn-flex-width bbn-vmiddle bbn-spadding">
    <div><?= _('Name') ?></div>
    <bbn-input bbn-model="currentName"
               class="bbn-flex-fill bbn-left-sspace"
               :disabled="isSaving"/>
  </div>
  <div class="bbn-flex-fill bbn-hspadding bbn-bottom-spadding">
    <bbn-rte bbn-model="currentText"
             class="bbn-100"
             :disabled="isSaving"/>
  </div>
  <bbn-loader bbn-if="isSaving"
              class="bbn-overlay"/>
</div>