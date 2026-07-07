(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      },
      mode: {
        type: String,
        required: true,
        validator(value){
          return ['correct', 'rewrite', 'entity_reply'].includes(value);
        }
      }
    },
    data(){
      return {
        rootUrl: appui.plugins['appui-email'] + '/',
        isLoading: true,
        formSource: {
          content: ''
        },
        requestId: false
      }
    },
    computed: {
      formButtons(){
        return this.isLoading ? ['cancel'] : ['cancel', 'submit'];
      }
    },
    methods: {
      success(){
        this.$emit('accept', this.formSource.content);
      },
      cancel(){
        if (this.requestId) {
          bbn.fn.abort(this.requestId);
          this.requestId = false;
        }
        this.$emit('cancel');
      },
      load(){
        if (this.mode && !this.requestId) {
          this.isLoading = true;
          const url = this.rootUrl + 'webmail/actions/ai/' + this.mode;
          this.requestId = bbn.fn.getRequestId(url, this.source, 'json');
          this.post(
            url,
            this.source,
            d => {
              if (d.success && d.data?.length) {
                this.formSource.content = d.data;
              }
              else {
                appui.error(d.error || bbn._('Error processing AI request'));
                this.getRef('form').cancel();
              }

              this.requestId = false;
              this.isLoading = false;
            },
            () => {
              appui.error();
              this.getRef('form').cancel();
              this.requestId = false;
              this.isLoading = false;
            }
          );
        }
      }
    },
    mounted(){
      this.load();
    }
  }
})();