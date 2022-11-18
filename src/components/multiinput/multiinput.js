// Javascript Document

(() => {
  return {
    mixins: [
      bbn.vue.basicComponent,
      bbn.vue.listComponent,
      bbn.vue.dropdownComponent
    ],
    props: {

    },
    data() {
      return {
        items: [],
        currentText: "",
      }
    },
    methods: {
      setEvent() {
        let element = this.getRef('autocomplete').getRef('element');
        bbn.fn.log("EVENT", element);
      },
      onPressBackspace(e) {
        bbn.fn.log("BACKSPACE", e.target);
        if (!e.target.value && this.items.length) {
          this.items.pop();
        }
      },
      select(data) {
        bbn.fn.log(data);
        if (!data.name) {
          data.name = data.email;
        }
        if (bbn.fn.search(this.items, {id: data.id}) == -1) {
          this.items.push(data);
        }
        this.$nextTick(() => {
          this.getRef('autocomplete').resetDropdown();
        });
      },
      close(item) {
        const idx = bbn.fn.search(this.items, {id: item.id})
        this.items.splice(idx, 1);
      },
      isEmail: bbn.fn.isEmail,
      clickContainer() {
        const autocomplete = this.getRef('autocomplete');
        if (autocomplete) {
          autocomplete.$refs.input.$refs.element.focus();
        }
      }
    }
  }
})();