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
        type: Array,
        default() {
          return [];
        }
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
        default() {
          return [];
        }
      },
      signatures: {
        type: Array,
        default() {
          return [];
        }
      },
      attachment: {
        type: Array,
        default() {
          return [];
        }
      },
      ai: {
        type: Boolean,
        default(){
          return !!appui.plugins['appui-ai'];
        }
      },
      entities: {
        type: Array,
        default() {
          return [];
        }
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
        timestamp: bbn.fn.microtimestamp(),
        currentPriority: this.source.priority || 3,
        priorityList: [{
          value: 1,
          text: '<span class="bbn-red">' + bbn._('Highest') + '</span>'
        }, {
          value: 2,
          text: '<span class="bbn-orange">' + bbn._('High') + '</span>'
        }, {
          value: 3,
          text: '<span class="bbn-green">' + bbn._('Normal') + '</span>'
        }, {
          value: 4,
          text: '<span class="bbn-blue">' + bbn._('Low') + '</span>'
        }, {
          value: 5,
          text: '<span class="bbn-grey">' + bbn._('Lowest') + '</span>'
        }],
        aiRewriteSource: [{
          text: bbn._("Friendly"),
          icon: "nf nf-md-emoticon_happy_outline",
          action: () => {
            this.aiRewrite('friendly');
          }
        }, {
          text: bbn._("Professional"),
          icon: "nf nf-md-briefcase_outline",
          action: () => {
            this.aiRewrite('professional');
          }
        }, {
          text: bbn._("Concise"),
          icon: "nf nf-md-format_line_spacing",
          action: () => {
            this.aiRewrite('concise');
          }
        }]
      };
    },
    computed: {
      aiEntityReplySource(){
        return bbn.fn.map(this.entities, e => {
          return {
            text: e.text,
            icon: "nf nf-md-message_arrow_right_outline",
            action: () => {
              this.aiEntityReply(e.value);
            }
          };
        });
      }
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
              priority: this.currentPriority
            }
          };
          if (this.replyTo?.length) {
            obj.email.in_reply_to = `<${this.replyTo}>`;
            obj.email.references = bbn.fn.map([...this.references, this.replyTo], r => `<${r}>`).join(' ');
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
            priority: this.currentPriority
          }
        };

        if (this.replyTo?.length) {
          obj.email.in_reply_to = `<${this.replyTo}>`;
          obj.email.references = bbn.fn.map([...this.references, this.replyTo], r => `<${r}>`).join(' ');
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
      },
      aiCorrect(){
        if (this.ai) {
          this.getPopup({
            label: false,
            closable: false,
            component: 'appui-email-webmail-write-ai',
            componentOptions: {
              mode: 'correct',
              source: {
                text: this.message
              }
            },
            componentEvents: {
              accept: message => {
                this.aiOnAccept(message);
              }
            },
            width: '85%',
            height: '85%',
            scrollable: false
          });
        }
      },
      aiRewrite(style){
        if (this.ai) {
          this.getPopup({
            label: false,
            closable: false,
            component: 'appui-email-webmail-write-ai',
            componentOptions: {
              mode: 'rewrite',
              source: {
                text: this.message,
                style
              }
            },
            componentEvents: {
              accept: message => {
                this.aiOnAccept(message);
              }
            },
            width: '85%',
            height: '85%',
            scrollable: false
          });
        }
      },
      aiEntityReply(idEntity){
        if (this.ai && this.source?.id && bbn.fn.isUid(idEntity)) {
          this.getPopup({
            label: false,
            closable: false,
            component: 'appui-email-webmail-write-ai',
            componentOptions: {
              mode: 'entity_reply',
              source: {
                id: this.source.id,
                id_entity: idEntity
              }
            },
            componentEvents: {
              accept: message => {
                this.aiOnAccept(message);
              }
            },
            width: '85%',
            height: '85%',
            scrollable: false
          });
        }
      },
      aiOnAccept(message){
        if (this.ai && message?.length) {
          this.oldMessage = this.message;
          this.message = message;
        }
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