(() => {
  return {
    props: {
      idAccount: {
        type: String,
        required: true
      },
      idFolder: {
        type: String,
        required: true
      },
      name: {
        type: String,
        required: true
      },
      folders: {
        type: Array,
        default(){
          return []
        }
      }
    },
    data() {
      return {
        root: appui.plugins["appui-email"] + "/",
        formData: {
          name: "",
          id_account: this.idAccount,
          id: this.idFolder,
        }
      }
    },
    methods: {
      onSubmit(ev){
        if (this.folders.includes(this.formData.name)) {
          ev.preventDefault();
          appui.error(bbn._("A folder with this name already exists"));
        }
      },
      success(d) {
        this.$emit('success', d);
      },
      failure(d) {
        appui.error(bbn._(d.error || "Cannot create the folder"));
      }
    }
  }
})();