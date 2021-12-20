<div class="bbn-overlay">
  <div class="bbn-flex-fill main-grid-fields">

    <div class="grid-menu-editor bbn-flex-fill">
      <bbn-button :text="type"
                  :notext="true"
                  icon="nf nf-mdi-format_text"
                  @click="setType('bbn-rte')"></bbn-button>
      <bbn-button :text="type"
                  :notext="true"
                  icon="nf nf-dev-markdown"
                  @click="setType('bbn-markdown')"></bbn-button>
      <bbn-button :text="type"
                  :notext="true"
                  icon="nf nf-mdi-format_text"></bbn-button>
    </div>

    <div class="bbn-flex-fill mail-grid-fields email-header">
      <bbn-button :text="trlt.to"></bbn-button>
      <bbn-input v-model="to"></bbn-input>
      <bbn-button text="CC"
                  v-if="ccButton"></bbn-button>
      <bbn-input v-model="CC"
                 v-if="ccButton"></bbn-input>
      <bbn-button text="CCI"
                  v-if="cciButton"></bbn-button>
      <bbn-input v-model="CCI"
                 v-if="cciButton"></bbn-input>
      <span class="span-text-center">{{trlt.subject}}</span>
      <bbn-input v-model="subject"></bbn-input>
    </div>
    <div class="bbn-flex-fill box">
      <div class="bbn-flex-width">
        <bbn-button text="CC"
                    @click="ccChange"></bbn-button>
        <bbn-button text="CCI"
                    @click="cciChange"></bbn-button>
      </div>
      <div style="flex: 1 1 auto">
        <bbn-button text="Send"
                    @click="send"
                    icon="nf nf-fa-send"
                    style="width:100%;height:100%"></bbn-button>
      </div>
    </div>
  </div>

  <div class="bbn-flex-height">
    <component :is="type"
               v-model="message"
               style="width: 80%; margin-left: 10%"></component>
  </div>
</div>
