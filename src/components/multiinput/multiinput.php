<!-- HTML Document -->

<div :class="[componentClass, 'bbn-textbox', 'bbn-w-100']"
     style="overflow: hidden"
     @click="clickContainer">
  <div v-for="item in items" class="bbn-alt-background bbn-radius bbn-border bbn-block bbn-hpadding bbn-vxspadding bbn-xsmargin" :title="item.email">
    <div class="bbn-top-right"
         @click="close(item)">
      <i class="nf nf-fa-times bbn-s"/>
    </div>
    <span v-text="item.name"></span>
  </div>
  <bbn-autocomplete class="bbn-alt-background bbn-block bbn-h-100 bbn-width-flex"
                    :source="source"
                    :autobind="false"
                    :source-text="sourceText"
                    @change="select"
                    v-model="currentText"
                    ref="autocomplete"
                    >
  </bbn-autocomplete>
</div>