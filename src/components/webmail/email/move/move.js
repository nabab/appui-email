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
    }
  }
})();