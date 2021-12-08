<div class="bbn-overlay bbn-flex-height">
  <div class="bbn-header">
    <bbn-toolbar class="bbn-m">
      <bbn-button></bbn-button>
      <bbn-switch v-model="switchValue"
                  value="markdown"
                  novalue="rte"></bbn-switch>
    </bbn-toolbar>
  </div>
  <div class="bbn-flex-fill bbn-grid-fields">
		<bbn-initial
                 :letters="trlt.to"></bbn-initial>
    <bbn-input v-model="replyTo"></bbn-input>
    <bbn-initial
                 letters="CC"></bbn-initial>
    <bbn-input v-model="CC"></bbn-input>
    <bbn-initial
                 letters="CCI"></bbn-initial>
    <bbn-input v-model="CCI"></bbn-input>
    <bbn-initial
                 :letters="trlt.subject"></bbn-initial>
    <bbn-input v-model="subject"></bbn-input>
  </div>
  <div class="bbn-flex-fill">
    <bbn-markdown v-model="emailHeader + source.html"
                  v-if=" switchValue === 'markdown'"></bbn-markdown>
    <bbn-rte v-model="emailHeader + source.html"
             v-if=" switchValue === 'rte' "></bbn-rte>
  </div>
</div>
