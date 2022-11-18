// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true,
        validator(v) {
          return v && v.id_account
        }
      }
    },
    data() {
      return {
        root: appui.plugins["appui-email"] + "/",
        formData: {
          name: this.source.name || "",
          folders: this.source.folders,
          id_account: this.source.id_account,
        }
      }
    },
    methods: {
      success(d) {
        appui.success(bbn._("Folder created with success"));
        let webmail = this.closest('bbn-container').getComponent();
				let idx = bbn.fn.search(webmail.source.accounts , { id: this.source.id_account });
        webmail.source.accounts.splice(idx, 1, d.account);
        let tree = webmail.getRef('tree');
        webmail.setTreeData();
        tree.updateData().then(() => {
          tree.reload()
        })
      },
      failure(d) {
        appui.error(bbn._(d.error || "Cannot create the folder"));
      }
    }
  }
})();