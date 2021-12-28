<div class="bbn-overlay">
  <div class="bbn-flex-fill" style="margin-top:0.2em">
    <div class="bbn-flex-fill main-grid-fields">

      <div class="grid-menu-editor bbn-flex-fill">
        <bbn-button text="rte"
                    :notext="true"
                    icon="nf nf-mdi-format_text"
                    @click="setType('bbn-rte')"></bbn-button>
        <bbn-button text="markdown"
                    :notext="true"
                    icon="nf nf-dev-markdown"
                    @click="setType('bbn-markdown')"></bbn-button>
        <bbn-button text="textarea"
                    :notext="true"
                    icon="nf nf-fa-file_text"
                    @click="setType('bbn-textarea')"></bbn-button>
      </div>

      <div class="bbn-flex-fill mail-grid-fields email-header">
        <bbn-button :text="trlt.to"
                    @click="openContacts"></bbn-button>
        <bbn-input v-model="currentTo"></bbn-input>
        <bbn-button :text="trlt.cc"
                    v-if="ccButton"></bbn-button>
        <bbn-input v-model="currentCC"
                   v-if="ccButton"></bbn-input>
        <bbn-button :text="trlt.cci"
                    v-if="cciButton"></bbn-button>
        <bbn-input v-model="currentCCI"
                   v-if="cciButton"></bbn-input>
        <span class="span-text-center">{{trlt.subject}}</span>
        <bbn-input v-model="subject"></bbn-input>
      </div>
      <div class="bbn-flex-fill box">
        <div class="bbn-flex-width">
          <bbn-button :text="trlt.cc"
                      @click="ccButton = !ccButton"></bbn-button>
          <bbn-button :text="trlt.cci"
                      @click="cciButton = !cciButton"></bbn-button>
        </div>
        <div style="flex: 1 1 auto;margin-top:0.7em">
          <bbn-button text="Send"
                      @click="send"
                      icon="nf nf-fa-send"
                      style="width:100%;height:100%"></bbn-button>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="bbn-100">
    <div style="width: 100%; margin-top:1%; margin-bottom:3%"
         class="bbn-flex-fill">
      <bbn-upload v-model="attachments"
                  :save-url="rootUrl + '/actions/email/upload_file'"
                  style="width: 100%"
                  @success="uploadSuccess"></bbn-upload>
    </div>
    <component :is="type"
               v-model="message"
               style="width: 80%; margin-left:10%; height: 95%"></component>
  </div>
</div>
