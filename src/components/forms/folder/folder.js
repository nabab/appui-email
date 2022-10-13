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
          id_parent: this.source.id_parent || null,
          id_account: this.source.id_account,
        }
      }
    },
    methods: {
      success(d) {
        bbn.fn.log(d);
      }
    }
  }
})();