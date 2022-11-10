// Javascript Document

(() => {
  return {
    props: {
      signatures: {
        required: true,
        type: Array,
      },
      selected: {
        required: true,
      }
    },
    data() {
      return {
        // index of the selected signature
        currentSignature: null,
        currentText: "",
        currentName: "",
        originalText: "",
        originalName: "",
        type: "bbn-rte",
        isSaving: false,
        types: [
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "bbn-textarea", text: bbn._('Text')}
        ],
        root: appui.plugins['appui-email'] + '/'
      };
    },
    beforeMount() {
      const selectedRow = bbn.fn.getRow(this.signatures, {id: this.selected})
      if (selectedRow) {
        this.currentSignature = this.selected
      }
    },
    computed: {
      currentDropdownSigns() {
        const ar = this.signatures.map(a => {
          return {
            value: a.id,
            text: a.name
          }
        })
        // push in front of array
        ar.unshift({value: null, text: "New"})
        return ar;
      },
      isSaved() {
        return (this.originalName === this.currentName) && (this.originalText === this.currentText)
      },
      selectedRow() {
        if (this.currentSignature) {
          return bbn.fn.getRow(this.signatures, {id: this.currentSignature})
        }
        return null;
      }
    },
    methods: {
      deleteSign() {
        bbn.fn.log(this.currentSignature)
        if (this.currentSignature) {
          this.confirm(bbn._('Do you want to delete this signature ?'), () => {
            bbn.fn.post(this.root + 'actions/signatures/delete', {
              id: this.currentSignature
            }, (d) => {
              if (d.success) {
                appui.success(bbn._('Successfully deleted'))
                const idx = bbn.fn.search(this.signatures, {id: this.currentSignature})
                this.signatures.splice(idx, 1);
                if (idx == 0 && !this.signatures.length) {
                  this.currentSignature = null;
                } else if (idx !== 0) {
                  this.currentSignature -= 1;
                } else if (idx !== this.signatures.length + 1) {
                  this.currentSignature += 1;
                }
              } else {
                appui.error(bbn._('Error in signature delete'));
              }
            })
          })
        }
      },
      saveSign() {
        if (this.currentSignature) {
          bbn.fn.post(this.root + 'actions/signatures/update', {
            id: this.currentSignature,
            signature: this.currentText,
            name: this.currentName
          }, (d) => {
            bbn.fn.log("UPDATE", d);
            if (d.success) {
              appui.success(bbn._('Successfully saved'))
              this.isSaving = true;
              const idx = bbn.fn.search(this.signatures, {id: this.currentSignature})
              this.signatures.splice(idx, 1, d.signature);
              setTimeout(() => {
                this.isSaving = false
              }, 50);
            } else {
              appui.error(bbn._('Error in signature save'));
            }
          })
        } else {
          bbn.fn.post(this.root + 'actions/signatures/create', {
            signature: this.currentText,
            name: this.currentName
          }, (d) => {
            bbn.fn.log("CREATE", d);
            if (d.success) {
              appui.success(bbn._('Successfully Saved'))
              this.signatures.push(d.signature)
              this.currentSignature = d.signature.id
            } else {
              appui.error(bbn._('Error in signature save'));
            }
          })
        }
      },
    },
    watch: {
      currentSignature() {
        this.currentText = this.currentSignature ? this.selectedRow.signature : ''
        this.currentName = this.currentSignature ? this.selectedRow.name : ''
        this.originalText = this.currentText;
        this.originalName = this.currentName;
      },
    },
  }
})()