// Javascript Document

(() => {
  return {
    data(){
      return {
        test: true,
        isWriteMail: false,
        url: bbn.env.host + '/'
      }
    },
    mounted() {
      bbn.fn.log("TEST", this.source.root, this.source)
    },
    computed: {
      reply() {
        
      },
    }
  }
})();