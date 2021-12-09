<div class="bbn-overlay bbn-flex-height email-header">
  <div class="bbn-header mail-editor-header">
    <bbn-toolbar class="bbn-m">
      <div class="bbn-flex-width">
            <bbn-button :text="trlt.editor"
                :disabled="true"
                class="subjectButton"
                style=" margin-right: 1%; border:none;"></bbn-button>
        <bbn-dropdown v-model="type"
                      :source="editors"></bbn-dropdown>
      </div>
      <div class="bbn-flex-width">
      </div>
      <div class="bbn-flex">
        <bbn-button text="CC"
                    @click="ccChange"
                    :style="{color : ccButton ? 'green' : 'red'}"></bbn-button>
        <bbn-button text="CCI"
                    @click="cciChange"
                    :style="{color : cciButton ? 'green' : 'red'}"></bbn-button>
        <bbn-button :text="trlt.send"
                    icon="nf nf-fa-send"
                    style="color: blue"></bbn-button>
      </div>
    </bbn-toolbar>
  </div>
  <div class="bbn-flex-fill mail-grid-fields email-header">
    <bbn-button :text="trlt.to"></bbn-button>
    <bbn-input v-model="replyTo"></bbn-input>
    <bbn-button text="CC"
                v-if="ccButton"></bbn-button>
    <bbn-input v-model="CC"
               v-if="ccButton"></bbn-input>
    <bbn-button text="CCI"
                v-if="cciButton"></bbn-button>
    <bbn-input v-model="CCI"
               v-if="cciButton"></bbn-input>
    <bbn-button :text="trlt.subject"
                :disabled="true"
                class="subjectButton"
                style="border:none;"></bbn-button>
    <bbn-input v-model="subject"></bbn-input>
  </div>
  <div class="bbn-flex-fill">
        <component :is="'bbn-' + type"
                    v-model="source.html"></component>
  </div>
</div>
