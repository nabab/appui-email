<!-- HTML Document -->
<div :class="['appui-email-webmail-item', 'bbn-flex-column', 'bbn-radius', 'bbn-p', 'bbn-reactive', 'bbn-spadding', {'bbn-selected-background bbn-selected-text' : isSelected}]"
     @click="select">
  <span class="bbn-bottom-xspadding bbn-ellipsis"
        :style="{ fontWeight: source.is_read ? 'normal' : 'bold'}">
    {{source.subject}}
  </span>
  <div class="bbn-flex-width bbn-vmiddle">
    <div class="bbn-flex-fill">
      <span class="bbn-small bbn-secondary-text-alt">
        <a bbn-if="extractedFrom?.length && extractedFrom[0].email"
          :href="'mailto:' + extractedFrom[0].email"
          :title="(extractedFrom[0].name ? extractedFrom[0].name + ' ' : '') + extractedFrom[0].email"
          bbn-text="extractedFrom[0].name || extractedFrom[0].email"
          class="bbn-w-100 bbn-ellipsis"/>
        <span bbn-else
              class="bbn-w-100 bbn-ellipsis"
              bbn-text="source.from"/>
      </span>
    </div>
    <div class="bbn-small">
      <i bbn-if="source.priority && (source.priority !== 3)"
        :class="['nf nf-md-exclamation_thick', priorityColor]"
        :title="priorityText"/>
      <i bbn-if="source.attachments?.length"
        class="nf nf-fa-paperclip"/>
      <i bbn-if="source.thread?.length > 1"
        class="nf nf-fa-reply"/>
      <i bbn-if="source.is_read"
        class="nf nf-fa-envelope_open_o"/>
      <i bbn-else
        class="nf nf-fa-envelope"/>
    </div>
  </div>
  <div class="bbn-vxspadding">
    <span class="appui-email-webmail-item-excerpt bbn-small bbn-ellipsis"
          bbn-html="excerpt"/>
  </div>
  <div class="appui-email-webmail-item-footer bbn-vmiddle bbn-xs">
    <span bbn-text="formatDate(source.date)"/>
    <a bbn-if="extractedTo?.length && extractedTo[0].email"
      :href="'mailto:' + extractedTo[0].email"
      :title="toTitle"
      bbn-text="toText"/>
    <span bbn-else
          bbn-text="source.to"/>
  </div>
</div>
