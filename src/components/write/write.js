// Javascript Document

(() => {
  let translate =  {
    from: bbn._('From'),
    send: bbn._('Send'),
    to: bbn._('To'),
    subject: bbn._('Subject'),
    unknown: bbn._('Unknown'),
  };
  return {
    props: {
      source: {
        required: true,
        type: Object,
      },
      subject: {
        type: String,
        default: "",
      },
      to: {
        type: String,
        default: "",
      },
      CC: {
        type: String,
        default: "",
      },
      CCI: {
        type: String,
        default: "",
      }
    },
    data() {
      return {
        trlt: {
          from: bbn._('From'),
          send: bbn._('Send'),
          to: bbn._('To'),
          subject: bbn._('Subject'),
          unknown: bbn._('Unknown'),
          editor: bbn._('Editor'),
        },
        ccButton: false,
        cciButton: false,
        type: "rte",
        message: (this.source.html && this.source.html != "") ? this.source.html : this.source.plain,
      };
    },
    mounted() {
      bbn.fn.log("test", this.source);
     	if (this.source.login.includes('@bbn.so')) {
        let tmp = this.source.login.split('@');
        this.to = this.to.replace(tmp[0] + '@bbn.so' + ' ', '');
         this.to = this.to.replace(tmp[0] +  '@bbn.solutions' + ' ', '');
      } else {
         this.to = this.to.replace(this.source.login + ' ', '');
      }
    },
    methods: {
      ccChange() {
        if (this.ccButton) {
          this.ccButton = false;
        } else {
          this.ccButton = true;
        }
      },
      cciChange() {
        if (this.cciButton) {
          this.cciButton = false;
        } else {
          this.cciButton = true;
        }
      },
      createEmailListString(array) {
        let res = "";
        for (let i = 0; i < array.length;i++) {
          res += array[i].mailbox + '@' +array[i].host + ' ';
        }
        return res;
      },
      send() {
        bbn.fn.post(appui.plugins['appui-email'] + 'actions/email/send', {
          message: this.message,
          to: this.replyTo,
          cc: this.CC,
          cci: this.CCI
        }, )
      }
    }
  }
})()