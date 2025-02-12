(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      identifier: {
        type: String,
        required: true
      },
      uid: {
        type: String,
        required: true
      },
      mailbox: {
        type: String,
        required: true
      },
      from: {
        type: String,
        required: true
      }
    },
    data(){
      return {
        isLoading: false,
        root: appui.plugins['appui-email'] + '/',
        currentEntities: [],
      }
    },
    methods: {
      getEntities(){
        this.isLoading = true;
        this.post(this.root + 'webmail/entities', {
          id: this.identifier,
          uid: this.uid,
          mailbox: this.mailbox,
          from: this.from
        }, d => {
          this.isLoading = false;
        }, () => {
          this.isLoading = false;
        });
      }
    },
    mounted(){
      this.getEntities();
    },
    watch: {
      id(){
        this.getEntities();
      }
    }
  }
})();