// Javascript Document

(() => {
  return {
    props: {
      signatures: {
        required: true,
        type: Array,
      },
      dropdownSignatures: {
        required: true,
        type: Array,
      },
      currentIdx: {
        required: true,
        type: String,
      }
    },
    data() {
      return {
        currentSignatures: this.signatures.length ? this.signatures.slice() : [{
          name: 'New',
          signature: ''
        }],
        currentDropdownSigns: this.dropdownSignatures,
        currentSignature: this.currentIdx === "null" ? "0" : this.currentIdx,
        isChanged: false,
        currentText: "",
        currentName: "",
        type: "bbn-rte",
        types: [
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "bbn-textarea", text: bbn._('Text')}
        ],
        root: appui.plugins['appui-email'] + '/'
      };
    },
    mounted() {
      bbn.fn.log(this.currentSignatures);
      bbn.fn.log('idx', this.currentIdx, this.currentSignature, this.currentSignatures);
      if (this.currentIdx === "null") {
        this.currentDropdownSigns.push({text: this.currentSignatures[0].name, value: "0"})
      }
      this.currentText = this.currentSignatures[this.currentSignature].signature
      this.currentName = this.currentSignatures[this.currentSignature].name
    },
    computed: {
      notSavedExist() {
        for (let i in this.currentSignatures) {
          if (!this.currentSignatures[i].id)
            return true;
        }
        return false;
      },
    },
    methods: {
      resetSigns() {
        this.confirm(bbn._('All unsaved signatures will be deleted. Continue ?'), () => {
          for (let i = 0; i < this.currentSignatures.length; i++) {
            if (this.currentSignatures[i].id === undefined) {
              bbn.fn.log(this.currentSignatures[i]);
              this.currentSignature = i - 1;
              this.currentSignatures.splice(i, 1);
              this.currentDropdownSigns.splice(i, 1);
              i--;
            }
          }
        })
      },
      createSign() {
        let a = 0;
        for (let i in this.currentSignatures) {
          if (this.currentSignatures[i].name === (a ? `New ${a}` : 'New')) {
            a += 1
          }
        }
        let name;
        if (a == 0) {
          name = "New"
        } else {
          name = `New ${a}`
        }
        this.currentSignatures.push({
          name: name,
          signature: ''
        })
        this.currentDropdownSigns.push({
          text: name,
          value: this.currentSignatures.length - 1
        })
        this.currentSignature = this.currentSignatures.length - 1
      },
      deleteSign() {
        if (this.currentSignatures.length) {
          this.confirm(bbn._(`Do you really want to remove the Name signature ${this.currentSignatures[this.currentSignature]}`), () => {
            let signature = this.currentSignatures[this.currentSignature];
            if (!signature.id) {
              let idx = this.currentSignature
              if (this.currentSignature) {
                this.currentSignature = `${idx - 1}`;
              } else {
                this.currentSignature = null
                this.currentDropdownSigns = []

              }
              this.currentSignatures.splice(idx, 1);
              this.currentDropdownSigns.splice(idx, 1);
            } else {
              bbn.fn.post(this.root + 'actions/signatures/delete', {
                id: signature.id
              }, (d) => {
                let idx = this.currentSignature
                if (!d.success) {
                  appui.success(bbn._('Signature deleted'))
                  if (this.currentSignature) {
                    this.currentSignature = `${idx - 1}`;
                    this.currentDropdownSigns.splice(idx, 1);
                  } else {
                    this.currentSignature = null;
                    this.currentDropdownSigns = []
                  }
                  this.currentSignatures.splice(idx, 1);
                  let componentWrite = appui.find('appui-email-write');
                  componentWrite.updateSign();
                } else {
                  appui.error(bbn._('Impossible to delete signature'))
                }
              })
            }
          })
        }
      },
      saveSign() {
        let signature = this.currentSignatures[this.currentSignature]
        if (signature.id) {
          bbn.fn.post(this.root + 'actions/signatures/update', {
            id: signature.id,
            name: this.currentName,
            signature: this.currentText
          }, (d) => {
            if (!d.success) {
              appui.success(bbn._('Signature saved'))
              let idx = this.currentSignature
              this.$set(this.currentSignatures, idx, {id: d.id, name: this.currentName, signature: this.currentText})
            } else {
              appui.error(bbn._('Signature save error'))
            }
          })
        } else {
          bbn.fn.post(this.root + 'actions/signatures/create', {
            name: this.currentName,
            signature: this.currentText
          }, (d) => {
            if (d.success) {
              appui.success(bbn._('Signature saved'))
              let idx = this.currentSignature
              this.$set(this.currentSignatures, idx, {id: d.id, name: this.currentName, signature: this.currentText})
              let componentWrite = appui.find('appui-email-write');
              componentWrite.updateSign();
              bbn.fn.log(this.currentSignatures, this.currentText, this.currentName)

            } else {
              appui.error(bbn._('Signature save error'))
            }
          })
        }
      },
      close() {
        this.closest('bbn-floater').close();
      },
      signToWatch() {
        return this.currentSignatures[this.currentSignature].signature
      }
    },
    watch: {
      currentSignature() {
        if (this.currentSignature != "null") {
          this.currentText = this.currentSignatures[this.currentSignature].signature
          this.currentName = this.currentSignatures[this.currentSignature].name
        }
      },
      currentName() {
        if (this.currentSignature != "null") {
          bbn.fn.log(this.currentDropdownSigns)
          this.$set(this.currentDropdownSigns[this.currentSignature], "text", this.currentName)

        }
      }
    },
  }
})()