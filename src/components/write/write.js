// Javascript Document

(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      account: {
        type: String
      },
      replyTo: {
        type: String
      },
      references: {
        type: String
      },
      isReply: {
        required: true,
        type: Boolean
      },
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
      },
      attachment: {
        type: Array,
        defaut: [],
      }
    },
    data() {
      let currentAccount = this.account;
      if (!this.account) {
        const webmail = appui.getRegistered('appui-email-webmail');
        if (webmail) {
          currentAccount = webmail.currentAccount;
        }
      }

      return {
        rootUrl: appui.plugins['appui-email'] + '/',
        ccButton: true,
        cciButton: false,
        attachmentsModel: [],
        attachments: this.attachment,
        currentTo: this.to?.length ? bbn.fn.clone(this.to) : '',
        currentCC: this.CC?.length ? bbn.fn.clone(this.CC) : '',
        currentCCI: this.CCI?.length ? bbn.fn.clone(this.CCI) : '',
        currentAccount: currentAccount || this.accounts[0]?.value || '',
        currentSignature: this.signatures.length ?
          this.signatures[0].id :
          null,
        type: "bbn-rte",
        types: [
          {value: "bbn-rte", text: bbn._('Rich text editor')},
          {value: "bbn-markdown", text: bbn._('Markdown')},
          {value: "bbn-textarea", text: bbn._('Text')}
        ],
        currentSubject: this.subject,
        message: this.source.html?.length ?
          bbn.fn.clone(this.source.html) :
          bbn.fn.clone(this.source.plain),
        originalMessage: this.message || "",
        messageTypeIcon: "nf nf-seti-html",
        messageTypeText: 'html',
        timestamp: bbn.fn.microtimestamp()
      };
    },
    methods: {
      // try to find the original mail if is found add the signature beetween original mail and new mail otherwise add the signature at the front
      addSignature() {
        if (this.currentSignature) {
          const signature = bbn.fn.getRow(this.signatures, {id: this.currentSignature});
          // check if message is a response from an email
          if (this.originalMessage) {
            // trying to replace the email responded
            let canAddAtEnd = false;
            this.message.replace(this.originalMessage, (token) => {
              canAddAtEnd = true;
              return "";
            })
            // if email found replace it by the signature and place it after the signature
            if (canAddAtEnd) {
              this.message = this.message.replace(this.originalMessage, '\n\n\n' + signature.signature + '\n') + this.originalMessage;
            // else put the signature at the top of the email
            } else {
              this.message = '\n\n\n' + signature.signature + '\n' + this.message
            }
          // if is a new email
          } else {
            // check if user have writed email and place tha signature at the end
            if (this.message) {
            	this.message = this.message + '\n\n\n' + signature.signature + '\n'
            // otherwise place it directly because the email is empty
            } else {
              this.message = '\n\n\n' + signature.signature + '\n'
            }
          }
        }
      },
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
        if (this.currentTo?.length
          && (this.currentSubject.length
            || this.message.length
          )
        ) {
          const obj = {
            id_account: this.currentAccount,
            email: {
              title: this.currentSubject,
              text: this.message,
              to: this.currentTo,
              cc: this.currentCC,
              bcc: this.currentCCI,
              attachments: this.attachments.concat(bbn.fn.map(bbn.fn.clone(this.attachmentsModel), a => a.path)),
              important: 0
            }
          };
          if (this.replyTo?.length) {
            obj.email.in_reply_to = `<${this.replyTo}>`;
            obj.email.references = this.references ? this.references + ` <${this.replyTo}>` : `<${this.replyTo}>`;
          }

          this.post(this.rootUrl + 'actions/email/send', obj);
        }
      },
      saveDraft(){

      },
      openContacts(type) {
        this.getPopup({
          component:  'appui-email-popup-contacts',
          label: bbn._('Address book'),
          width: '35vw',
          height: '50vh',
          componentOptions: {
            component: this.getRef(type + 'Input')
          }
        });
      },
      openSignatureEditor(action) {
        bbn.fn.log('idx', this.currentSignature);
        this.getPopup({
          component: 'appui-email-popup-editsignatures',
          label: bbn._('Signature Editor'),
          width: '60vw',
          height: '60vh',
          componentOptions: {
            signatures: this.signatures,
            selected: this.currentSignature
          }
        })
      },
      updateSign() {
        bbn.fn.post(appui.plugins['appui-email'] + '/actions/signatures/get', {}, (d) => {
          if (d.success) {
            this.signature = d.res;
          } else {
            appui.error(bbn._('Impossible to update signatures'))
          }
        })
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