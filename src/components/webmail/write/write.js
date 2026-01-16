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
        currentSignature: null,
        currentSubject: this.subject,
        message: this.source.html?.length ?
          bbn.fn.clone(this.source.html) :
          bbn.fn.clone(this.source.plain),
        originalMessage: this.message || "",
        timestamp: bbn.fn.microtimestamp()
      };
    },
    methods: {
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

          this.post(this.rootUrl + 'webmail/actions/email/send', obj, d => {
            if (d.success) {
              appui.success(bbn._('Email sent successfully'));
              this.closest('bbn-container').close();
            }
            else {
              appui.error(bbn._('Error sending email'));
            }
          });
        }
      },
      saveDraft(){
        const obj = {
          id_account: this.currentAccount,
          id: this.source?.id || null,
          uid: this.source?.msg_unique_uid || null,
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
        this.post(this.rootUrl + 'webmail/actions/email/draft', obj, d => {
          if (d.success) {
            if (d.id) {
              this.source.id = d.id;
            }

            if (d.uid) {
              this.source.msg_unique_uid = d.uid;
            }

            appui.success(bbn._('Email saved successfully'));
          }
          else {
            appui.error(bbn._('Error saving email'));
          }
        });
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
        this.getPopup({
          component: 'appui-email-webmail-write-signatures',
          label: bbn._('Signature Editor'),
          width: '60vw',
          height: '60vh',
          componentOptions: {
            source: this.signatures,
            selected: this.currentSignature
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
    },
    watch: {
      signatures(){
        this.getRef('signatures').updateData();
      },
      currentSignature(newVal){
        const ele = this.getRef('editor').querySelector('.bbn-rte-element > .__bbn__signature');
        if (ele) {
          let signature = '';
          if (newVal) {
            signature = bbn.fn.getField(this.signatures, 'signature', {id: this.currentSignature}) || '';
          }

          ele.innerHTML = signature.length ? signature + '<br>' : signature;
        }
      }
    }
  }
})()