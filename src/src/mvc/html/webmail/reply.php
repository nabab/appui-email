<div class="bbn-overlay bbn-flex-height email-header">
  <div class="bbn-flex-fill mail-grid-fields email-header">
    <bbn-button :text="trlt.to"></bbn-button>
    <bbn-input v-model="replyTo"></bbn-input>
    <bbn-button text="CC"
                @click="ccChange"></bbn-button>
    <bbn-button text="CCI"
                @click="cciChange"></bbn-button>
    <bbn-button text="CC"
                v-if="ccButton"></bbn-button>
    <bbn-input v-model="CC"
               v-if="ccButton"></bbn-input>
    <span v-if="ccButton"></span>
    <span v-if="ccButton"></span>
    <bbn-button text="CCI"
                v-if="cciButton"></bbn-button>
    <bbn-input v-model="CCI"
               v-if="cciButton"></bbn-input>
    <span v-if="cciButton"></span>
    <span v-if="cciButton"></span>
      <bbn-button :text="trlt.subject"
                  :disabled="true"
                  class="subjectButton"
                  style="border:none;"></bbn-button>
    <bbn-input v-model="subject"></bbn-input>
    <bbn-button :text="trlt.send"
                icon="nf nf-fa-send"
                @click="send"></bbn-button>
    <bbn-context>
    	<bbn-button :text="type"
                  :disabled="true"></bbn-button>
    </bbn-context>
    <span></span>
  </div>
  <div class="bbn-flex-fill">
    <component :is="'bbn-' + type"
               v-model="message"
               style="width: 80%; margin-left: 10%"></component>
  </div>
</div>
