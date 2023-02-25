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
      }
    },
    mounted() {
      bbn.fn.log(this.source.is_read);
    },
    methods: {
      formatDate(date) {
        let emailDate = new Date(date);
        let currentDate = new Date();

        if (emailDate.getFullYear() !== currentDate.getFullYear()) {
          // If the email date year is not the same as the current year, format with the year
          return emailDate.toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
        } else if (emailDate.getDate() === currentDate.getDate()) {
          // If the email date is today, format with time only
          return emailDate.toLocaleTimeString("en-US", { hour: "2-digit", minute: "2-digit" });
        } else {
          // Otherwise, format with month and day only
          return emailDate.toLocaleDateString("en-US", { month: "short", day: "numeric" });
        }
      },
      select() {
        let webmail = this.closest('appui-email-webmail');
        webmail.selectMessage(this.source);
        this.source.is_read = 1;
      },
      extractNameAndEmail(str) {
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
        return null;
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