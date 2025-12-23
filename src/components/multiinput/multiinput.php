<!-- HTML Document -->
<div :class="[componentClass, 'bbn-textbox', 'bbn-flex-wrap']"
     style="overflow: hidden"
     @click="clickContainer">
  <div bbn-for="item in items"
       class="bbn-alt-background bbn-radius bbn-border bbn-hxspadding bbn-xsmargin bbn-vmiddle"
       :title="item">
    <span bbn-text="item"/>
    <i class="nf nf-fa-close bbn-xs bbn-left-sspace bbn-p"
       @click="removeItem(item)"/>
  </div>
  <bbn-autocomplete class="bbn-alt-background bbn-flex-fill"
                    :source="source"
                    :autobind="false"
                    :source-text="sourceText"
                    @keyup="onAutocompleteKeyup"
                    @change="select"
                    @blur="onAutocompleteBlur"
                    bbn-model="currentText"
                    ref="autocomplete"
                    :limit="100"/>
</div>