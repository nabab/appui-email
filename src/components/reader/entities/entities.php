<div class="appui-email-reader-entities bbn-block">
  <div bbn-if="currentEntities.length">
    <span><?=_("Save as entity's note")?></span>
    <bbn-dropdown :source="currentEntities"
                  bbn-model="currentEntity"/>
    <bbn-button icon="nf nf-fa-plus"
                @click="addToEntity"
                :notext="true"
                :disabled="!currentEntity"/>
  </div>
</div>