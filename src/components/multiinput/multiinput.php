<!-- HTML Document -->
<div :class="[componentClass, 'bbn-textbox', 'bbn-flex-wrap']"
     style="overflow: hidden"
     @click="clickContainer">
  <div bbn-for="item in items"
       class="bbn-alt-background bbn-radius bbn-border bbn-hxspadding bbn-xsmargin bbn-vmiddle"
       :title="item">
    <span bbn-text="item"/>
    <i class="nf nf-fa-close bbn-xs bbn-left-sspace bbn-p"
       @click="close(item)"/>
  </div>
  <bbn-autocomplete class="bbn-alt-background"
                    :source="source"
                    :autobind="false"
                    style="max-height: 2.5rem;"
                    :source-text="sourceText"
                    @keydown.delete="onPressBackspace"
                    @change="select"
                    bbn-model="currentText"
                    ref="autocomplete"/>
</div>