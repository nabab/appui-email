// Javascript Document

(() => {
  return {
    props: {
     	source: {
        type: Object
      }
    },
    mounted() {

    },
    methods: {
      download(filename) {
        bbn.fn.download(appui.plugins['appui-email'] + "/data/attachment/index/" + this.source.id_account + '/' + this.source.id + '/' + filename);
      }
    }
  }
})();