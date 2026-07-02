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
        entities: [],
        entitiesWithNote: [],
        entity: ''
      }
    },
    computed: {
      currentEntities(){
        return bbn.fn.map(
          bbn.fn.clone(this.entities),
          e => {
            if (bbn.fn.getRow(this.entitiesWithNote, {value: e.value})) {
              e.disabled = true;
            }

            return e;
          }
        )
      }
    },
    methods: {
      getEntities(){
        this.isLoading = true;
        this.entity = '';
        this.entities = [];
        this.entitiesWithNote = [];
        this.post(this.root + 'webmail/entities', {
          id: this.identifier,
          uid: this.uid,
          mailbox: this.mailbox,
          mail: this.mail
        }, d => {
          if (d.success
            && (d.id === this.identifier)
          ) {
            this.entities = d.data?.entities || [];
            this.entitiesWithNote = d.data?.entitiesWithNote || [];
          }

          this.isLoading = false;
        }, () => {
          this.isLoading = false;
        });
      },
      addToEntity(){
        if (this.entity && !this.isLoading) {
          this.confirm(bbn._("Are you sure you want to save this mail as an entity's note?"), () => {
            this.isLoading = true;
            this.post(this.root + 'webmail/entities', {
              id: this.identifier,
              mailbox: this.mailbox,
              idEntity: this.entity
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