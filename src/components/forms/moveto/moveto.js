// Javascript Document

(() => {
  return {
    props : {
      source : {
  			type: Array,
        default: [],
      },
      model : {
        type: String,
        required: true,
        default: "",
      }
    },
    data() {
      return {
       	folderId: "",
      }
    },
    methods:  {
      getData() {
        return {
          id: this.id,
          folderId: this.folderId,
        }
      }
    }
  }
})();