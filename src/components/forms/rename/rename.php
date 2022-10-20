<!-- HTML Document -->

<bbn-form :source="formData"
          :action="root + 'actions/folder/rename'"
          class="bbn-padding bbn-lg"
          @success="success"
          @failure="failure">
	<bbn-input v-model="formData.name"
						 class="bbn-wide"/>
</bbn-form>