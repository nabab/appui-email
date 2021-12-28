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
      },
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
          cc: bbn._('CC'),
          cci: bbn._('CCI'),
        },
        rootUrl: appui.plugins['appui-email'],
        ccButton: false,
        cciButton: false,
        attachments: [],
        currentTo: "",
        currentCC: "",
        currentCCI: "",
        type: "bbn-rte",
        message: (this.source.html && this.source.html != "") ? this.source.html : this.source.plain,
        messageTypeIcon: "nf nf-seti-html",
        messageTypeText: 'html',
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
      this.currentTo = this.to;
      this.currentCC = this.CC;
      this.currentCCI = this.CCI;
    },
    methods: {
      setType(type) {
        this.type = type;
        if (type == "bbn-textarea") {
          this.message = this.source.plain;
        } else {
          this.message = this.source.html;
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
          id_account: this.source.id_account,
          email: {
            title: this.subject,
            text: this.message,
            to: this.replyTo,
            cc: this.CC,
            cci: this.CCI,
            attachments: this.attachments,
            important: 0,
            
          }
        })
      },
      openContacts() {
        this.getPopup({
          component:  'appui-email-popup-contacts',
          title: bbn._('Address book'),
					width: '35vw',
          height: '50vh',
        });
      },
      uploadSuccess(field, fileName, responseData, response) {
        this.attachments.push(responseData.path);
      },
      currentToSetter(newValue) {
        this.currentTo = newValue;
      }
    }
  }
})()