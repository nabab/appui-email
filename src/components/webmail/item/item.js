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
    computed: {
      isSelected() {
        let webmail = this.closest('appui-email-webmail');
        return webmail.selectedMessageIDisSame(this.source.id);
      },
      toText(){
        let res = '';
        if (this.extractedTo?.length) {
          res = this.extractedTo[0].name || this.extractedTo[0].email;
          if (this.extractedTo.length > 1) {
            res += ` +${this.extractedTo.length - 1}`;
          }
        }

        return res;
      },
      toTitle(){
        let res = '';
        if (this.extractedTo?.length) {
          bbn.fn.each(this.extractedTo, a => {
            res += (res.length ? '\n' : '') + (a.name ? a.name + ' ' : '') + a.email;
          });
        }

        return res;
      }
    },
    methods: {
      formatDate(date) {
        return bbn.dt(date).calendar();
      },
      select() {
        let webmail = this.closest('appui-email-webmail');
        webmail.selectMessage(this.source);
        this.source.is_read = 1;
      },
      extractNameAndEmail(str) {
        let res = [];
        if (str?.length) {
          str = str.replace(/"/g, '');
          const regex = /(?:([A-Za-zÀ-ÖØ-öø-ÿ'’.-]+(?:\s+[A-Za-zÀ-ÖØ-öø-ÿ'’.-]+)*)\s*)?<\s*([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})\s*>/g;
          res = [...str.matchAll(regex)].map(m => ({
            name: m[1] ?? null,
            email: m[2]
          }));
        }

        return res;
      }
    }
  }
})();