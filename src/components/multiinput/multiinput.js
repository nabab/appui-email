// Javascript Document

(() => {
  return {
    mixins: [
      bbn.cp.mixins.basic,
      bbn.cp.mixins.input,
      bbn.cp.mixins.list,
      bbn.cp.mixins.dropdown
    ],
    props: {
      asArray: {
        type: Boolean,
        default: false
      }
    },
    data() {
      let items = [];
      if (bbn.fn.isString(this.value)
        && this.value.length
      ) {
        items = this.value.split(this.value.includes(';') ? ';' : ',');
      }
      else if (bbn.fn.isArray(this.value)) {
        items.push(...this.value);
      }

      return {
        items,
        currentText: "",
      }
    },
    methods: {
      isEmail: bbn.fn.isEmail,
      onAutocompleteKeyup(e) {
        bbn.fn.log('keyup', e.keyCode, e.key, e.code, e);
        switch (e.key) {
          case ',':
          case ';':
            if (e.target.value?.length) {
              this.select({email: e.target.value});
            }

            break;
          case 'Backspace':
            if (!e.target.value && this.items.length) {
              this.items.pop();
            }

            break;
        }
      },
      onAutocompleteBlur(e){
        if (e.target.value) {
          this.select({email: e.target.value});
        }
      },
      select(data) {
        if (data?.email?.length) {
          let email = data.email.trim().replace(',', '').replace(';', '');
          if (this.isEmail(email)
            && !this.items.includes(email)
          ) {
            this.items.push(email);
            this.emitInput(this.asArray ? this.items : this.items.join(';'));
          }
        }

        this.$nextTick(() => {
          this.getRef('autocomplete').resetDropdown();
        });
      },
      unselect(data) {
        if (data?.email?.length) {
          let email = data.email.trim().replace(',', '').replace(';', '');
          if (this.items.includes(email)) {
            const idx = this.items.indexOf(email);
            this.items.splice(idx, 1);
            this.emitInput(this.asArray ? this.items : this.items.join(';'));
          }
        }

        this.$nextTick(() => {
          this.getRef('autocomplete').resetDropdown();
        });
      },
      removeItem(item) {
        const idx = this.items.indexOf(item);
        if (idx > -1) {
          this.items.splice(idx, 1);
          this.emitInput(this.asArray ? this.items : this.items.join(';'));
        }
      },
      clickContainer() {
        const autocomplete = this.getRef('autocomplete');
        if (autocomplete) {
          autocomplete.getRef('input').getRef('element').focus();
        }
      }
    }
  }
})();