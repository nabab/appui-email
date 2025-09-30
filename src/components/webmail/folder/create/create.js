(() => {
  return {
    props: {
      idAccount: {
        type: String,
        required: true
      },
      idParent: {
        type: String
      }
    },
    data() {
      return {
        root: appui.plugins["appui-email"] + "/",
        formData: {
          name: '',
          id_parent: this.idParent || null,
          id_account: this.idAccount
        }
      }
    },
    methods: {
      success(d) {
        this.$emit('success', d);
      },
      failure(d) {
        appui.error(bbn._(d.error || "Cannot create the folder"));
      }
    }
  }
})();