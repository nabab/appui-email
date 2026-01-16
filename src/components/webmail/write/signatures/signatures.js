// Javascript Document

(() => {
  return {
    props: {
      source: {
        required: true,
        type: Array,
      },
      selected: {
        type: String
      }
    },
    data() {
      const currentSign = this.selected ?
        bbn.fn.getRow(this.source, {id: this.selected}) :
        null;
      return {
        root: appui.plugins['appui-email'] + '/',
        currentSignature: currentSign?.id || 'new',
        currentText: currentSign?.signature || '',
        currentName: currentSign?.name || '',
        original: {
          text: currentSign?.signature || '',
          name: currentSign?.name || ''
        },
        isSaving: false,
      };
    },
    computed: {
      isNew(){
        return this.currentSignature === 'new';
      },
      signatures() {
        const ar = bbn.fn.clone(this.source);
        ar.unshift({id: 'new', name: bbn._('New')});
        return ar;
      },
      isSaved() {
        return (this.original.name === this.currentName)
          && (this.original.text === this.currentText);
      }
    },
    methods: {
      newSign(){
        if (!this.isSaved) {
          this.confirm(bbn._("You haven't saved your last changes, are you sure you want to continue?"), () => {
            this.resetAll();
          });
        }
        else {
          this.resetAll();
        }
      },
      deleteSign() {
        if (this.currentSignature) {
          this.confirm(bbn._('Do you want to delete this signature?'), () => {
            this.isSaving = true;
            this.post(this.root + 'webmail/actions/signatures/delete', {
              id: this.currentSignature
            }, d => {
              if (d.success) {
                const idx = bbn.fn.search(this.source, {id: this.currentSignature})
                this.source.splice(idx, 1);
                this.$nextTick(() => {
                  this.getRef('signDropdown').updateData();
                  if (this.source.length) {
                    this.currentSignature = this.source[this.source.length - 1].id;
                  }
                  else {
                    this.currentSignature = 'new';
                  }

                  this.setOriginal();
                  appui.success();
                  this.isSaving = false;
                })
              }
              else {
                appui.error();
                this.isSaving = false;
              }
            }, () => {
              appui.error();
              this.isSaving = false;
            });
          })
        }
      },
      saveSign() {
        this.confirm(bbn._('Do you want to save this signature?'), () => {
          this.isSaving = true;
          this.post(
            this.root + 'webmail/actions/signatures/' + (this.isNew ? 'create' : 'update'),
            {
              id: this.currentSignature,
              signature: this.currentText,
              name: this.currentName
            },
            d => {
              if (d.success && d.data?.id) {
                const idx = bbn.fn.search(this.source, {id: d.data.id});
                if (idx > -1) {
                  this.source.splice(idx, 1, d.data);
                }
                else {
                  this.source.push(d.data);
                }

                this.$nextTick(() => {
                  this.getRef('signDropdown').updateData();
                  this.currentSignature = d.data.id;
                  this.setOriginal();
                  appui.success();
                  this.isSaving = false;
                })
              }
              else {
                appui.error();
                this.isSaving = false;
              }
            },
            () => {
              appui.error();
              this.isSaving = false;
            }
          );
        });
      },
      setOriginal(){
        this.original.text = this.currentText;
        this.original.name = this.currentName;
      },
      resetAll(){
        this.currentSignature = 'new';
        this.currentText = '';
        this.currentName = '';
        this.setOriginal();
      }
    },
    watch: {
      currentSignature(newVal) {
        const obj = bbn.fn.getRow(this.source, {id: newVal});
        this.currentText = obj ? obj.signature : '';
        this.currentName = obj ? obj.name : '';
        this.setOriginal();
      },
    },
  }
})()