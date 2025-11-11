(() => {
  return {
    props: {
      source: {
        type: Object,
        default(){
          return {
            name: '',
            host: '',
            encryption: '',
            port: '',
            login: '',
            pass: ''
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
          value: '',
          port: 25
        }, {
          text: bbn._('SSL'),
          value: 'ssl',
          port: 465
        }, {
          text: bbn._('TLS'),
          value: 'tls',
          port: 587
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
      'source.encryption'(newVal){
        if (!this.source.port) {
          this.source.port = bbn.fn.getField(this.encryptions, 'port', 'value', newVal);
        }
      }
    }
  }
})();