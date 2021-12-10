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
      bbn.fn.log("test", this.source.to, this.source.from, this.from, this.to);
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