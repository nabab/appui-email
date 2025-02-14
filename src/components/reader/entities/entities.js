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
      mail: {
        type: String,
        required: true
      }
    },
    data(){
      return {
        isLoading: false,
        root: appui.plugins['appui-email'] + '/',
        currentEntities: [],
        currentEntity: ''
      }
    },
    methods: {
      getEntities(){
        this.isLoading = true;
        this.currentEntity = '';
        this.currentEntities = [];
        this.post(this.root + 'webmail/entities', {
          id: this.identifier,
          uid: this.uid,
          mailbox: this.mailbox,
          mail: this.mail
        }, d => {
          if (d.success
            && (d.id === this.identifier)
          ) {
            this.currentEntities = d.entities;
          }

          this.isLoading = false;
        }, () => {
          this.isLoading = false;
        });
      },
      addToEntity(){
        if (this.currentEntity && !this.isLoading) {
          this.confirm(bbn._("Are you sure you want to save this mail as an entity's note?"), () => {
            this.isLoading = true;
            this.post(this.root + 'webmail/entities', {
              id: this.identifier,
              mailbox: this.mailbox,
              idEntity: this.currentEntity
            }, d => {
              this.isLoading = false;
              if (d.success
                && (d.id === this.identifier)
              ) {
                appui.success();
                this.getEntities();
              }
              else {
                appui.error();
              }
            }, () => {
              this.isLoading = false;
            });
          });
        }
      }
    },
    mounted(){
      this.getEntities();
    },
    watch: {
      identifier(){
        this.getEntities();
      }
    }
  }
})();