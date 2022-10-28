<div class="bbn-overlay">
  <div class="bbn-flex-fill container bbn-bordered bbn-radius">
    <div class="bbn-flex-fill container__top">
      <div>
        <bbn-button :text="trlt.to"
                    @click="openContacts('to')"></bbn-button>
      </div>
      <div>
        <appui-email-multiinput :source="rootUrl + '/webmail/contacts'"
                                source-text="name"
                                source-value="id">
        
        </appui-email-multiinput>
      </div>
      <div>
        <bbn-button v-if="!ccButton"
                    :text="trlt.cc"
                    @click="ccButton = !ccButton"></bbn-button>
      </div>
      <div v-if="ccButton">
        <bbn-button :text="trlt.cc"
                    @click="openContacts('cc')"></bbn-button>
      </div>
      <div v-if="ccButton">
        <bbn-input class="bbn-w-90"
                   v-model="currentCC"
                   v-if="ccButton"></bbn-input>
      </div>
      <div v-if="ccButton">
        <bbn-button v-if="!cciButton"
                    :text="trlt.cci"
                    @click="cciButton = !cciButton"></bbn-button>
      </div>
      <div v-if="cciButton">
        <bbn-button :text="trlt.cci"
                    @click="openContacts('cci')"></bbn-button>
      </div>
      <div v-if="cciButton">
        <bbn-input class="bbn-w-90"
                   v-model="currentCCI"></bbn-input>
      </div>
      <div v-if="cciButton">

      </div>
      <div class="span-text-center">
        <span class="bbn-lg">{{trlt.subject}}</span>
      </div>
      <div>
        <bbn-input v-model="subject"
                   style="width: 100%">
        </bbn-input>
      </div>
      <div>

      </div>
    </div>
    <div class="bbn-flex-fill container__toolbar">

      <bbn-upload v-model="attachmentsModel"
                  :save-url="rootUrl + '/actions/email/upload_file'"
                  style="width: 30%"
                  @success="uploadSuccess"></bbn-upload>

      <div class="span-text-center">
        <span class="bbn-lg">{{trlt.from}}</span>
        <bbn-dropdown v-model="currentFrom"
                      :source="accounts"></bbn-dropdown>
      </div>

      <div class="span-text-center">
        <span class="bbn-lg">{{trlt.editor}}</span>
        <bbn-dropdown v-model="type"
                      :source="types"></bbn-dropdown>
      </div>

    </div>
    <div class="bbn-flex-fill container__editor">
      <component :is="type"
                 v-model="message"
                 style="min-height: 40vh; width: 100%"></component>
    </div>
    <div class="bbn-flex-fill container__bottom">
      <bbn-button text="Send"
                  @click="send"
                  icon="nf nf-fa-send"></bbn-button>
    </div>
  </div>
</div>