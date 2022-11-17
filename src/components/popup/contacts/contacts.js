// Javascript Document

(() => {
  return {
    props: {
      component: {
        required: true,
        type: Object,
      }
    },
    data() {
      return {
        root: appui.plugins['appui-email'] + '/'
      }
    },
    methods: {
      rowClicked(col, colIndex, dataIndex) {
       	this.component.select(col);
        this.closest('bbn-floater').close();
      }
    }
  }
})()