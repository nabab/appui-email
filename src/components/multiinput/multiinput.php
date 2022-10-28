<!-- HTML Document -->

<div :class="[componentClass, 'bbn-textbox', 'bbn-w-100']"
     @click="clickContainer">
  <div v-for="item in items" class="bbn-alt-background bbn-radius bbn-border bbn-block bbn-hpadding
                                    bbn-vxspadding bbn-xsmargin">
    <div class="bbn-top-right"
         @click="close(item)">
      <i class="nf nf-fa-times bbn-s"/>
    </div>
    <span v-text="item.name"></span>
  </div>
  <bbn-autocomplete class="bbn-block"
                    :source="source"
                    :source-text="sourceText"
                    @change="select"
                    v-model="currentTo"
                    style="width: 100px"
                    ref="autocomplete"
                    >
  </bbn-autocomplete>
</div>