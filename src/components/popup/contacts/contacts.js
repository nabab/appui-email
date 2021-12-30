// Javascript Document

(() => {
  return {
    props: {
      type: {
        required: true,
        type: String,
      }
    },
    data() {
      return {
        root: appui.plugins['appui-email'] + '/'
      }
    },
    methods: {
      rowClicked(col, colIndex, dataIndex) {
        let componentWrite = appui.find('appui-email-write');
        if (this.type === 'to') {
          componentWrite.currentToSetter((componentWrite.currentTo == "") ?  col.email : componentWrite.currentTo + ' ' + col.email);
        } else if (this.type === 'cc') {
          componentWrite.currentCCSetter((componentWrite.currentCC == "") ? col.email : componentWrite.currentCC + ' ' + col.email);
        } else if (this.type === 'cci') {
          componentWrite.currentCCISetter((componentWrite.currentCCI == "") ? col.email : componentWrite.currentCCI + ' ' + col.email);
        }
        this.closest('bbn-floater').close();
      }
    }
  }
})()