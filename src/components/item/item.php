<!-- HTML Document -->


<div class="bbn-email-item bbn-bg-grey bbn-black bbn-flex-height">
  <div class="card bbn-bg-grey bbn-flex-height">
    <span class="bbn-w-100 bbn-spadded bbn-ellipsis subject"
          style="text-align: center;">
      {{source.subject}}
    </span>
    <div class="bbn-w-100 bbn-flex-width bbn-spadding menu">
      <div class="bbn-flex-fill">
        <span class="bbn-blue from">
          {{source.from}}
        </span>
      </div>
      <div class="icons">
        <i v-if="source.flags"
           class="bbn-red nf nf-fa-exclamation"/>
        <i v-if="source.attachments"
           class="nf nf nf-md-paperclip"/>
        <i v-if="source.is_read != 0"
           class="nf nf-fae-envelope_open_o"/>
        <i v-else
           class="nf nf-fa-envelope"/>
      </div>
    </div>
    <div class="bbn-spadding bbn-w-100">
      <span class="text">
        {{source.text}}
      </span>
    </div>
    <div class="bbn-w-100 footer">
      <span class="bbn-spadding date">
        {{ formatDate(source.date) }}
      </span>
      <span class="bbn-spadding owner"
            style="margin-left: auto">
        {{ source.to }}
      </span>
    </div>
  </div>
</div>


