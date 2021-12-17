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
        replyTo: "",
        CC: "",
        ccButton: false,
        cciButton: false,
        CCI: "",
        subject: "TR: " +  this.source.subject,
        switchValue: "rte",
        editors: [
          "rte",
          "markdown",
          "textarea"
        ],
        type: "rte",
        message: this.source.html,
      };
    },
    mounted() {
      bbn.fn.log("test", this.source);
      this.replyTo = this.createEmailListString(this.source.from);
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
        bbn.fn.post(appui.plugins['appui-email'] + '/actions/email/send', {
          text: this.message,
          to: this.replyTo,
          cc: this.CC,
          cci: this.CCI,
          id_account: this.source.id_account
        }, )
      }
    }
  }
})()