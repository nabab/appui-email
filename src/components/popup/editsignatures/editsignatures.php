<!-- HTML Document -->

<div class="bbn-overlay">
  <bbn-toolbar class="bbn-left-lpadding">
    <div>
      <bbn-dropdown v-model="type"
                    :source="types"></bbn-dropdown>
    </div>
    <div/>
    <div>
      <bbn-dropdown v-if="!isSaving"
                    v-model="currentSignature"
                    :source="currentDropdownSigns"
                    :disabled="!isSaved"
                    ref="signDropdown"/>
    </div>
    <div/>
    <div>
      <!-- @click set the currentSignature to null because the new item have null id -->
      <bbn-button title="<?=_('New')?>"
                  icon="nf nf-mdi-plus"
                  :notext="true"
                  @click="currentSignature = null"
                  :disabled="!isSaved"/>
    </div>
    <div>
      <bbn-button title="<?=_('Delete')?>"
                  icon="nf nf-fa-trash"
                  :notext="true"
                  @click="deleteSign()"
                  :disabled="!currentSignature"/>
    </div>
    <bbn-button title="<?=_('Save')?>"
                icon="nf nf-mdi-content_save"
                :notext="true"
                @click="saveSign()"
                :disabled="isSaved"/>
  </bbn-toolbar>
  <div class="bbn-w-100 bbn-flex-width bbn-vmiddle">
    <div class="bbn-block bbn-padding"><?=_('Name')?></div>
    <div class="bbn-padding bbn-flex-fill">
      <bbn-input v-model="currentName"
                 class="bbn-w-100">
      </bbn-input>
    </div>
  </div>
  <div class="bbn-w-100 bbn-hpadding">
    <component :is="type"
               v-model="currentText"
               style="width: 100%;"></component>
  </div>
</div>