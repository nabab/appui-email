// Javascript Document

(() => {
  return {
    props: {
      component: {
        required: true
      }
    },
    data() {
      return {
        root: appui.plugins['appui-email'] + '/',
        selected: this.component.items
      }
    },
    methods: {
      onToggle(selected, data){
        bbn.fn.log("TOGGLE", selected, data);
        if (selected) {
          this.component.select(data);
        }
        else {
          this.component.unselect(data);
        }
      }
    }
  }
})()