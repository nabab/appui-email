(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        default(){
          return {
            folders: [],
            type: null,
            email: '',
            login: '',
            pass: '',
            host: '',
            smtp: '',
            ssl: 1,
            locale: true
          }
        }
      },
      types: {
        type: Array,
        required: true
      }
    },
    data(){
      return {
        root: appui.plugins['appui-email'] + '/',
        lastChecked: null,
        tree: [],
        accountChecker: null,
        errorState: false,
        currentPage: 1,
        isTesting: false,
        isDev: appui.user.isDev,
        inServerChanged: false,
        outServerChanged: false
      }
    },
    computed: {
      formButtons(){
        const btns = [];
        switch (this.currentPage) {
          case 1:
            btns.push('cancel', {
              label: bbn._('Next'),
              action: this.nextToTest,
              icon: 'nf nf-fa-arrow_circle_right',
              iconPosition: 'right'
            });
            break;

          case 2:
            btns.push({
              label: bbn._('Back'),
              action: this.backToConfig,
              icon: 'nf nf-fa-arrow_circle_left',
            }, 'submit');
            break;
        }

        return btns;
      },
      selectedFolders(){
        if (this.tree.length && this.source.folders.length) {
          return JSON.stringify(this.source.folders);
        }
        return '';
      },
      accountCode(){
        if (this.source.type) {
          return bbn.fn.getField(this.types, 'code', {id: this.source.type});
        }
        return null;
      }
    },
    methods: {
      backToConfig(){
        this.tree.splice(0);
        this.source.folders.splice(0);
        this.source.pass = '';
        this.errorState = false;
        this.isTesting = false;
        this.currentPage = 1;
      },
      nextToTest(){
        if (this.getRef('form')?.isValid()
          && this.source.email
          && bbn.fn.isEmail(this.source.email)
          && this.source.type
          && this.source.login
          && this.source.pass
        ) {
          this.tree.splice(0);
          this.isTesting = true;
          this.errorState = false;
          this.currentPage = 2;
          let ok = false;
          if (['imap', 'pop'].includes(this.accountCode)) {
            if (this.source.host
              && bbn.fn.isHostname(this.source.host)
              && this.source.smtp
              && bbn.fn.isHostname(this.source.smtp)
            ) {
              ok = true;
            }
          }
          else {
            ok = true;
          }

          if (ok) {
            this.post(
              this.root + 'webmail/actions/account',
              bbn.fn.extend({action: 'test'}, this.source),
              d => {
                if (d.data) {
                  let checked = [];
                  bbn.fn.each(d.data, a => {
                    if (a.subscribed) {
                      checked.push(a.uid);
                    }

                    this.tree.push(a);
                  });
                  this.isTesting = false;
                  this.$nextTick(() => {
                    this.source.folders = checked;
                    const tree = this.getRef('tree');
                    if (tree) {
                      tree.checked = this.source.folders;
                      tree.updateData();
                    }
                  });
                }
                else {
                  this.errorState = true;
                  this.isTesting = false;
                }
              },
              () => {
                this.errorState = true;
                this.isTesting = false;
              }
            );
          }
        }
      },
      success(d){
        this.$emit('success', d);
      },
      onServerFocus(on){
        if (bbn.fn.isString(on) && ['host', 'smtp'].includes(on)) {
          if (on === 'host' && !this.source.host.length && this.source.smtp.length) {
            this.source.host = this.source.smtp;
          }
          else if (on === 'smtp' && !this.source.smtp.length && this.source.host.length) {
            this.source.smtp = this.source.host;
          }
        }
      }
    },
    watch: {
      "source.host"(v){
        if (!this.outServerChanged) {
          this.source.smtp = v;
        }

        if (!v.length && this.inServerChanged) {
          this.inServerChanged = false;
        }
      },
      "source.smtp"(v){
        if (!this.inServerChanged) {
          this.source.host = v;
        }

        if (!v.length && this.outServerChanged) {
          this.outServerChanged = false;
        }
      },
      "source.email"(nv, ov) {
        if (nv) {
          if (ov === this.source.login) {
            this.source.login = nv;
          }
          else if (!this.source.login) {
            this.source.login = nv;
          }
        }
      }
    }
  }
})();