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
      cc: {
        type: String,
        default: "",
      },
      bcc: {
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
        bccButton: false,
        attachmentsModel: [],
        attachments: this.attachment,
        currentTo: this.to?.length ? bbn.fn.clone(this.to) : '',
        currentCc: this.cc?.length ? bbn.fn.clone(this.cc) : '',
        currentBcc: this.bcc?.length ? bbn.fn.clone(this.bcc) : '',
        currentAccount: currentAccount || this.accounts[0]?.value || '',
        currentSignature: null,
        currentSubject: this.subject,
        message: "",
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
        }],
        tmpElement: document.createElement('div'),
        includeQuote: this.isReply || !!this.source?.quote?.length
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
              cc: this.currentCc,
              bcc: this.currentBcc,
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
          id: this.source?.is_draft && this.source?.id ? this.source.id : null,
          uid: this.source?.is_draft && this.source?.msg_unique_uid ? this.source.msg_unique_uid : null,
          email: {
            title: this.currentSubject,
            text: this.message,
            to: this.currentTo,
            cc: this.currentCc,
            bcc: this.currentBcc,
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
            if (!d.id || !d.uid) {
              appui.error(bbn._('No draft id or uid returned'));
              return;
            }

            this.source.id = d.id;
            this.source.msg_unique_uid = d.uid;
            this.source.is_draft = 1;
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
      currentCcSetter(newValue) {
        this.currentCc = newValue;
      },
      currentBccSetter(newValue) {
        this.currentBcc = newValue;
      },
      aiCorrect(){
        if (this.ai) {
          const message = this.getClearMessage();
          if (!message?.length) {
            return;
          }

          this.getPopup({
            label: false,
            closable: false,
            component: 'appui-email-webmail-write-ai',
            componentOptions: {
              mode: 'correct',
              source: {
                text: message
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
          const message = this.getClearMessage();
          if (!message?.length) {
            return;
          }

          this.getPopup({
            label: false,
            closable: false,
            component: 'appui-email-webmail-write-ai',
            componentOptions: {
              mode: 'rewrite',
              source: {
                text: message,
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
        if (this.ai
          && this.source?.id
          && bbn.fn.isUid(idEntity)
        ) {
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
          this.tmpElement.innerHTML = message;
          if (this.currentSignature) {
            this.addSignature(this.currentSignature, true);
          }

          if (this.includeQuote) {
            if ((this.isReply && (this.source?.html?.length || this.source?.plain?.length))
              || (!this.isReply && this.source?.quote?.length)
            ) {
          }
            if (this.isReply) {
              const m = this.source.html?.length ? this.source.html : nl2br(this.source.plain);
              this.addQuote(m, true);
            }
            else {
              this.addQuote(this.source.quote, true);
            }
          }

          this.message = this.tmpElement.innerHTML;
        }
      },
      getClearMessage(){
        this.tmpElement.innerHTML = this.message;
        this.removeQuote(true);
        this.removeSignature(true);
        return this.tmpElement.innerHTML;
      },
      getEditorElement(){
        return this.getRef('editor').getRef('element');
      },
      getSignatureElement(){
        return this.getRef('editor').querySelector('.bbn-rte-element > div.__bbn__signature');
      },
      getSignatureBlock(content){
        return `<div class="__bbn__signature" style="margin-top: 20px;"><div>${content}</div></div>`
      },
      addSignature(signature, tmp = false) {
        if (signature?.length && bbn.fn.isUid(signature)) {
          signature = bbn.fn.getField(this.signatures, 'signature', {id: signature}) || '';
        }

        if (signature?.length) {
          if (!tmp) {
            this.tmpElement.innerHTML = this.message;
          }

          const signatureElement = this.tmpElement.querySelector(':scope > div.__bbn__signature');
          if (signatureElement) {
            signatureElement.querySelector(':scope > div').innerHTML = signature;
          }
          else {
            const quoteElement = this.tmpElement.querySelector(':scope > div.__bbn__quote');
            const sign = this.getSignatureBlock(signature);
            if (quoteElement) {
              quoteElement.insertAdjacentHTML('beforebegin', sign);
            }
            else {
              this.tmpElement.insertAdjacentHTML('beforeend', sign);
            }
          }

          if (!tmp) {
            this.message = (!this.message.length ? '<div><br></div>' : '') + this.tmpElement.innerHTML;
          }
        }
      },
      removeSignature(tmp = false) {
        if (!tmp) {
          this.tmpElement.innerHTML = this.message;
        }

        const signatureElement = this.tmpElement.querySelector(':scope > div.__bbn__signature');
        if (signatureElement) {
          signatureElement.remove();
        }

        if (!tmp) {
          this.message = this.tmpElement.innerHTML;
        }
      },
      getQuoteElement(){
        return this.getRef('editor').querySelector('.bbn-rte-element > div.__bbn__quote');
      },
      getQuoteBlock(content){
        return `<div class="__bbn__quote" style="margin-top: 20px;"><hr><blockquote type="cite">${content}</blockquote></div>`;
      },
      addQuote(quote, tmp = false) {
        if (quote?.length) {
          if (!tmp) {
            this.tmpElement.innerHTML = this.message;
          }

          const quoteElement = this.tmpElement.querySelector(':scope > div.__bbn__quote');
          if (quoteElement) {
            quoteElement.querySelector(':scope > blockquote').innerHTML = quote;
          }
          else {
            this.tmpElement.insertAdjacentHTML('beforeend', this.getQuoteBlock(quote));
          }

          if (!tmp) {
            this.message = (!this.message.length ? '<div><br></div>' : '') + this.tmpElement.innerHTML;
          }
        }
      },
      removeQuote(tmp = false) {
        if (!tmp) {
          this.tmpElement.innerHTML = this.message;
        }

        const quoteElement = this.tmpElement.querySelector(':scope > div.__bbn__quote');
        if (quoteElement) {
          quoteElement.remove();
        }

        if (!tmp) {
          this.message = this.tmpElement.innerHTML;
        }
      }
    },
    created() {
      if (this.source?.html?.length || this.source?.plain?.length) {
        const m = this.source.html?.length ? this.source.html : nl2br(this.source.plain);
        if (this.isReply && this.includeQuote) {
          this.addQuote(m);
        }
        else {
          this.message = m;
          if (this.source?.quote?.length) {
            this.addQuote(this.source.quote);
          }
        }
      }
    },
    watch: {
      signatures(){
        this.getRef('signatures').updateData();
      },
      currentSignature(newVal){
        if (newVal) {
          const signature = bbn.fn.getField(this.signatures, 'signature', {id: this.currentSignature}) || '';
          if (signature.length) {
            this.addSignature(signature);
          }
          else {
            this.removeSignature();
          }

        }
        else {
          this.removeSignature();
        }
      },
      includeQuote(newVal){
        if (this.isReply) {
          if (newVal && (this.source?.html?.length || this.source?.plain?.length)) {
            const m = this.source.html?.length ? this.source.html : nl2br(this.source.plain);
            this.addQuote(m);
          }
          else {
            this.removeQuote();
          }
        }
      }
    }
  }
})()