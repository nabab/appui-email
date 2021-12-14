<!-- HTML Document -->
<bbn-form submitText="Move"
          :source="data"
          action="emails/actions/email/change_folder">
	<bbn-dropdown :source="source.folders"
                v-model="folder"></bbn-dropdown>
</bbn-form>
