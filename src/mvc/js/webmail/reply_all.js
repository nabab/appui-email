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
          "markdown"
        ],
        type: "rte",
      };
    },
    mounted() {
      this.replyTo = this.createEmailListString(this.source.from) + this.createEmailListString(this.source.to);
      if (this.source.login.includes('@bbn.so')) {
        let tmp = this.source.login.split('@');
        this.replyTo = this.replyTo.replace(tmp[0] + '@bbn.so' + ' ', '');
         this.replyTo = this.replyTo.replace(tmp[0] +  '@bbn.solutions' + ' ', '');
      } else {
         this.replyTo = this.replyTo.replace(this.source.login + ' ', '');
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
      }
    }
  }
})()