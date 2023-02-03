// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      }
    },
    mounted() {
      bbn.fn.log(this.source.is_read);
    },
    methods: {
      formatDate(date) {
        let emailDate = new Date(date);
				let currentDate = new Date();
				if (emailDate.getDate() === currentDate.getDate()) {
          return emailDate.toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit'});
        } else {
          return emailDate.toLocaleDateString("en-US", {month: "short", day: "numeric"});
        }
      },
      select() {
        let webmail = this.closest('appui-email-webmail');
        webmail.selectMessage(this.source);
        this.source.is_read = 1;
      }
    }
  }
})();