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
        replyTo: this.source.from[0].mailbox + '@' + this.source.from[0].host,
        CC: "",
        CCI: "",
        subject: "RE: " +  this.source.subject,
        switch: false,
        type: "RTE",
        emailHeader: '<br><br><hr>' + translate.from  +  " : " +  (this.source.fromaddress ? this.source.fromaddress : translate.unknown) + '<br>' + translate.send +  " : " + (this.source.Date ? this.source.Date :  translate.unknown) + '<br>' + translate.to + " : " + (this.source.toaddress ? this.source.toaddress :  translate.unknown) + '<br>' + translate.subject + " : " + (this.source.Subject ? this.source.Subject :  translate.unknown) + '<br><br>',
      };
    },
    mounted() {
      bbn.fn.log("TEST", translate, this.source,  this.source.fromaddress, this.source.Date);
    },
    computed: {
      createEmailListString(array) {
      }
    }
  }
})()