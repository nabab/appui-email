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
        currentTo: null,
      }
    },
    methods: {
      select(data) {
        bbn.fn.log(arguments);
        this.items.push(data);
      },
      isEmail: bbn.fn.isEmail,
      clickContainer() {
        bbn.fn.log("IN");
        const autocomplete = this.getRef('autocomplete');
        if (autocomplete) {
          autocomplete.$refs.input.$refs.element.focus();
        }
      }
    }
  }
})();