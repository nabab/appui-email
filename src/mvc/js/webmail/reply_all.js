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
        },
        replyTo: "",
        CC: "",
        CCI: "",
        subject: "RE: " +  this.source.subject,
        switchValue: "rte",
        type: "RTE",
        emailHeader: '<br><br><hr>' + translate.from  +  " : " +  (this.source.from ? this.createEmailListString(this.source.from) : translate.unknown) + '<br>' + translate.send +  " : " + (this.source.Date ? this.source.Date :  translate.unknown) + '<br>' + translate.to + " : " + (this.source.to ? this.createEmailListString(this.source.to) : translate.unknown) + '<br>' + translate.subject + " : " + (this.source.Subject ? this.source.Subject :  translate.unknown) + '<br><br>',
      };
    },
    mounted() {
      //bbn.fn.log("TEST", translate, this.source,  this.source.from, this.source.reply_to);
      bbn.fn.log("test", this.source.to, this.source.from, this.from, this.to);

      this.replyTo = this.createEmailListString(this.source.from);
    },
    methods: {
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