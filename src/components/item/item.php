<!-- HTML Document -->


<div class="bbn-email-item bbn-flex-height"
     @click="select"
     :class="isSelected ? 'bbn-alt-background' : []">
  <div class="card bbn-flex-height">
    <span class="bbn-w-100 bbn-bottom-xspadding bbn-top-xspadding bbn-ellipsis subject"
          :style="{ fontWeight: source.is_read ? 'normal' : 'bold'}">
      {{source.subject}}
    </span>
    <div class="bbn-w-100 bbn-flex-width menu">
      <div class="bbn-flex-fill">
        <span class="bbn-small bbn-blue from">
          <a v-if="extractedFrom && extractedFrom.name && extractedFrom.email && extractedFrom.email !== extractedFrom.name" :href="'mailto:' + extractedFrom.email" :title="extractedFrom.email">{{extractedFrom.name}}</a>
          <a v-else-if="extractedFrom" :href="'mailto:' + extractedFrom.email" >{{extractedFrom.email}}</a>
          <span v-else>{{source.to}}</span>
        </span>
      </div>
      <div class="bbn-small icons">
        <i v-if="source.flags.includes('Highest')"
           class="bbn-red nf nf-fa-exclamation"/>
        <i v-if="source.flags.includes('High') && !source.flags.includes('Highest')"
           class="bbn-orange nf nf-fa-exclamation"/>
        <i v-if="source.attachments"
           class="nf nf nf-md-paperclip"/>
        <i v-if="source.is_read != 0"
           class="nf nf-fae-envelope_open_o"/>
        <i v-else
           class="nf nf-fa-envelope"/>
      </div>
    </div>
    <div class="bbn-w-100 bbn-vxspadding">
      <span class="text bbn-small">
        {{source.excerpt}}
      </span>
    </div>
    <div class="bbn-w-100 bbn-bottom-xspadding footer">
      <span class="bbn-xs date">
        {{ formatDate(source.date) }}
      </span>
      <span class="bbn-xs owner"
            style="margin-left: auto">
        <a v-if="extractedTo && extractedTo.name && extractedTo.email && extractedTo.email !== extractedTo.name" :href="'mailto:' + extractedTo.email" :title="extractedTo.email">{{extractedTo.name}}</a>
        <a v-else-if="extractedTo" :href="'mailto:' + extractedTo.email" >{{extractedTo.email}}</a>
        <span v-else>{{source.to}}</span>
      </span>
    </div>
    <div class="bbn-w-100 bbn-hr">

    </div>
  </div>
</div>


