<!-- HTML Document -->

<div class="bbn-w-100">
  <bbn-button v-for="attachment in source.attachments"
              v-bind:key="attachment.name"
              @click="download(attachment.name)"
              class="bbn-w-100">{{ attachment.name }}</bbn-button>
</div>
