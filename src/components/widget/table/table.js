// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-email'] + '/'
      }
    },
    mounted() {
      bbn.fn.log("lol", this.source);
    }
  }
})();