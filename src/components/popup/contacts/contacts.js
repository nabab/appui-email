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
        root: appui.plugins['appui-email'] + '/'
      }
    },
    methods: {
      rowClicked(col, colIndex, dataIndex) {
        bbn.fn.log(colIndex);
       	this.component.select(col);
        //this.closest('bbn-floater').close();
      }
    }
  }
})()