// Javascript Document

(() => {
  return {
    props : {
      source : {
  			type: Object,
        required: true,
      },
    },
    data() {
      return  {
        folder: (this.source.folders.length) ? this.source.folders[0].text : "",
        data: {
          id: this.source.id,
          folderId: (this.source.folders.length) ? this.source.folders[0].value : "",
        }
      }
    },
    mounted() {
      bbn.fn.log("SOURCE MOVETO", this.source, this.root);
    },
    watch: {
      folder() {
        this.data.folderId = this.folder;
      }
    }
  }
})();