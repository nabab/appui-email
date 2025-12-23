<!-- HTML Document -->
<div :class="['appui-email-webmail-item', 'bbn-flex-height', 'bbn-radius', 'bbn-p', 'bbn-reactive', {'bbn-alt-background' : isSelected}]"
     @click="select">
  <div class="card bbn-flex-height">
    <span class="bbn-w-100 bbn-bottom-xspadding bbn-top-xspadding bbn-ellipsis subject"
          :style="{ fontWeight: source.is_read ? 'normal' : 'bold'}">
      {{source.subject}}
    </span>
    <div class="bbn-w-100 bbn-flex-width menu">
      <div class="bbn-flex-fill">
        <span class="bbn-small bbn-blue from">
          <a bbn-if="extractedFrom?.length && extractedFrom[0].email" 
             :href="'mailto:' + extractedFrom[0].email"
             :title="(extractedFrom[0].name ? extractedFrom[0].name + ' ' : '') + extractedFrom[0].email"
             bbn-text="extractedFrom[0].name || extractedFrom[0].email"/>
          <span bbn-else>{{source.from}}</span>
        </span>
      </div>
      <div class="bbn-small icons">
        <i bbn-if="source.flags.includes('Highest')"
           class="bbn-red nf nf-fa-exclamation"/>
        <i bbn-if="source.flags.includes('High') && !source.flags.includes('Highest')"
           class="bbn-orange nf nf-fa-exclamation"/>
        <i bbn-if="source.attachments?.length"
           class="nf nf nf-md-paperclip"/>
        <i bbn-if="source.thread?.length > 1"
           class="nf nf-fa-reply"/>
        <i bbn-if="source.is_read"
           class="nf nf-fa-envelope_open_o"/>
        <i bbn-else
           class="nf nf-fa-envelope"/>
      </div>
    </div>
    <div class="bbn-w-100 bbn-vxspadding">
      <span class="text bbn-small bbn-ellipsis"
            bbn-html="excerpt"/>
    </div>
    <div class="bbn-w-100 bbn-bottom-xspadding footer">
      <span class="bbn-xs date">
        {{ formatDate(source.date) }}
      </span>
      <span class="bbn-xs owner"
            style="margin-left: auto">
        <a bbn-if="extractedTo?.length && extractedTo[0].email"
           :href="'mailto:' + extractedTo[0].email"
           :title="toTitle"
           class="to"
           bbn-text="toText"/>
        <span bbn-else
              class="to"
              bbn-text="source.to"/>
      </span>
    </div>
  </div>
</div>
