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
      accounts: {
        type: Array,
        default: [],
      },
      signatures: {
        type: Array,
        default: [],
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
          cc: bbn._('CC'),
          cci: bbn._('BCC'),
          signatures: bbn._('Signatures')
        },
        rootUrl: appui.plugins['appui-email'],
        ccButton: false,
        cciButton: false,
        attachmentsModel: [],
        attachments: [],
        currentTo: "",
        currentCC: "",
        currentCCI: "",
        currentFrom: (this.source && this.source.login) ? this.source.login : (this.accounts.length) ? this.accounts[0]: "",
        currentSignature: (this.signatures.length) ? "0": "null",
        currentSignatures: [],
        currentSourceSignatures: this.signatures,
        type: "bbn-rte",
        types: [
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "bbn-textarea", text: bbn._('Text')}
        ],
        message: (this.source.html && this.source.html != "") ? this.source.html : this.source.plain,
        messageTypeIcon: "nf nf-seti-html",
        messageTypeText: 'html',
      };
    },
    mounted() {
      bbn.fn.log("test", this.source, this.source.data);
      for (let i in this.signatures) {
        bbn.fn.log(this.signatures[i])
        this.currentSignatures.push({text: this.signatures[i].name, value: i})
      }
      bbn.fn.log(this.currentSignatures);
      if (this.source && this.source.login && this.source.login.includes('@bbn.so')) {
        this.to = this.to.replace(this.source.login + ' ', '');
      }
      this.currentTo = this.to;
      this.currentCC = this.CC;
      this.currentCCI = this.CCI;
      bbn.fn.log("ACCOUNT", this.accounts);
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
            to: this.currentTo.replaceAll(' ', ';'),
            cc: this.currentCC.replaceAll(' ', ';'),
            bcc: this.currentCCI.replaceAll(' ', ';'),
            attachments: this.attachments,
            important: 0,
          }
        })
      },
      openContacts(type) {
        this.getPopup({
          component:  'appui-email-popup-contacts',
          title: bbn._('Address book'),
          width: '35vw',
          height: '50vh',
          componentOptions: {
            type: type,
          }
        });
      },
      openSignatureEditor(action) {
        bbn.fn.log('idx', this.currentSignature);
        this.getPopup({
          component: 'appui-email-popup-editsignatures',
          title: bbn._('Signature Editor'),
          width: '60vw',
          height: '60vh',
          componentOptions: {
            signatures: this.currentSourceSignatures,
            dropdownSignatures: this.currentSignatures.length ? this.currentSignatures : [{
              text: 'New',
              value: 0
            }],
            currentIdx: this.currentSignature
          }
        })
      },
      updateSign() {
        bbn.fn.post(appui.plugins['appui-email'] + '/actions/signatures/get', {}, (d) => {
          if (d.success) {
            this.currentSignatures = []
            this.signature = d.res;
            for (let i in this.signatures) {
              this.currentSignatures.push({text: this.signatures[i].name, value: i})
            }
          } else {
            appui.error(bbn._('Impossible to update signatures'))
          }
        })
      },
      uploadSuccess(field, fileName, responseData, response) {
        this.attachments.push(responseData.path);
      },
      currentToSetter(newValue) {
        this.currentTo = newValue;
      },
      currentCCSetter(newValue) {
        this.currentCC = newValue;
      },
      currentCCISetter(newValue) {
        this.currentCCI = newValue;
      }
    }
  }
})()