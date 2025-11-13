(() => {
  return {
    props: {
      source: {
        type: Object,
        default(){
          return {
            name: '',
            host: '',
            login: '',
            pass: '',
            encryption: 'starttls',
            port: 587,
            validatecert: 1,
          }
        }
      },
      locale: {
        type: Boolean,
        default: false
      }
    },
    data(){
      return {
        root: appui.plugins['appui-email'] + '/',
        encryptions: [{
          text: bbn._('None'),
          value: 'none',
          port: 25,
          validatecert: 0
        }, {
          text: bbn._('TLS/SSL'),
          value: 'tls',
          port: 465,
          validatecert: 1
        }, {
          text: bbn._('STARTTLS'),
          value: 'starttls',
          port: 587,
          validatecert: 1
        }]
      }
    },
    methods: {
      onSuccess(d){
        this.$emit('success', d);
      },
      onFailure(d){
        this.$emit('failure', d);
      }
    },
    beforeMount(){
      if (!this.source.id
        && this.locale
      ) {
        this.source.locale = true;
      }
    },
    watch: {
      'source.encryption'(newVal, oldVal){
        const oldPort = bbn.fn.getField(this.encryptions, 'port', 'value', oldVal) || null;
        const oldValidateCert = bbn.fn.getField(this.encryptions, 'validatecert', 'value', oldVal) || null;
        if (!this.source.port || (oldPort === this.source.port)) {
          this.source.port = bbn.fn.getField(this.encryptions, 'port', 'value', newVal);
        }

        if (oldValidateCert === this.source.validatecert) {
          this.source.validatecert = bbn.fn.getField(this.encryptions, 'validatecert', 'value', newVal);
        }
      }
    }
  }
})();