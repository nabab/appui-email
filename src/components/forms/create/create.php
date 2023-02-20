<!-- HTML Document -->

<bbn-form :source="formData"
          :action="root + 'actions/folder/create'"
          class="bbn-padding bbn-lg"
          @success="success"
          @failure="failure">
</bbn-form>