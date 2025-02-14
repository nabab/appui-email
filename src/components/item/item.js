// Javascript Document

(() => {
  return {
    props: {
      source: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        extractedTo: this.extractNameAndEmail(this.source.to),
        extractedFrom: this.extractNameAndEmail(this.source.from),
        excerpt: this.source.excerpt.replaceAll(/\[cid\:.*\]/g, '')
      }
    },
    methods: {
      formatDate(date) {
        const emailDate = dayjs(date);
        const currentDate = dayjs();
        if (emailDate.format('DDMMYYYY') === currentDate.format('DDMMYYYY')) {
          return dayjs(date).format("LT");
        }
        else {
          return dayjs(date).format("L");
        }
      },
      select() {
        let webmail = this.closest('appui-email-webmail');
        webmail.selectMessage(this.source);
        this.source.is_read = 1;
      },
      extractNameAndEmail(str) {
        if (!str) {
          return "";
        }
        str = str.replace(/"/g, '');
        const nameRegex = /(.+) <(.+)>/;
        const nameMatch = str.match(nameRegex);
        if (nameMatch) {
          const [, name, email] = nameMatch;
          return { name, email };
        } else {
          const emailRegex = /([^\s@]+@[^\s@]+\.[^\s@]+)/;
          const emailMatch = str.match(emailRegex);
          if (emailMatch) {
            const email = emailMatch[0];
            return { email };
          }
        }
        return "";
      }
    },
    computed: {
      isSelected() {
        let webmail = this.closest('appui-email-webmail');
        return webmail.selectedMessageIDisSame(this.source.id);
      },
    }
  }
})();