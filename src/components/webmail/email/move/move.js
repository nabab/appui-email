(() => {
  return {
    props : {
      email: {
        type: String,
        required: true
      },
      folders : {
  			type: Array,
        required: true,
      },
    },
    data() {
      return  {
        root: appui.plugins['appui-email'] + '/',
        formSource: {
          id: this.email,
          id_folder: '',
        }
      }
    },
    methods: {
      onSuccess(d){
        if (d.success) {
          this.$emit('success', d, this.formSource.id_folder);
        }
        else {
          this.$emit('failure', d);
        }
      },
      onFailure(d){
        this.$emit('failure', d);
      }
    }
  }
})();