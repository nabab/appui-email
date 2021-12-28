// Javascript Document

(() => {
  return {
    data() {
      return {
        root: appui.plugins['appui-email'] + '/'
      }
    },
    methods: {
      rowClicked(col, colIndex, dataIndex) {
        let componentWrite = appui.find('appui-email-write');
        componentWrite.currentToSetter(componentWrite.currentTo + ' ' + col.email);
        this.closest('bbn-floater').close();
      }
    }
  }
})()