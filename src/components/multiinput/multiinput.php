<!-- HTML Document -->

<div :class="[componentClass, 'bbn-textbox', 'bbn-w-100']"
     style="overflow: hidden"
     @click="clickContainer">
  <div v-for="item in items" class="bbn-alt-background bbn-radius bbn-border bbn-block bbn-hpadding bbn-vxspadding bbn-xsmargin" :title="item.email">
    <div class="bbn-top-right"
         style="padding-right: 2px;"
         @click="close(item)">
      <i class="nf nf-fae-thin_close bbn-xs"/>
    </div>
    <span v-text="item.name"></span>
  </div>
  <bbn-autocomplete class="bbn-alt-background bbn-block bbn-h-100 bbn-width-flex"
                    :source="source"
                    :autobind="false"
                    style="max-height: 2.5rem;"
                    :source-text="sourceText"
                    @keydown.delete="onPressBackspace"
                    @change="select"
                    v-model="currentText"
                    ref="autocomplete"
                    >
  </bbn-autocomplete>
</div>