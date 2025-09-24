(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    data(){
      const cp = appui.getRegistered('appui-email-webmail');
      return {
        cp: cp,
        types: cp.source.types,
        hasSMTP: false,
        lastChecked: null,
        tree: [],
        accountChecker: null,
        errorState: false,
        account: cp.editedAccount || {
          folders: [],
          type: null,
          host: null,
          login: '',
          smtp: null,
          pass: '',
          ssl: 1,
          email: '',
          locale: true
        },
        currentPage: 1,
        isTesting: false,
        isDev: appui.user.isDev
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
        if (this.tree.length && this.account.folders.length) {
          return JSON.stringify(this.account.folders);
        }
        return '';
      },
      accountCode(){
        if (this.account.type) {
          return bbn.fn.getField(this.types, 'code', {id: this.account.type});
        }
        return null;
      }
    },
    methods: {
      backToConfig(){
        this.tree.splice(0);
        this.account.folders.splice(0);
        this.account.pass = '';
        this.errorState = false;
        this.isTesting = false;
        this.currentPage = 1;
      },
      nextToTest(){
        if (this.getRef('form')?.isValid()
          && this.account.email
          && bbn.fn.isEmail(this.account.email)
          && this.account.type
          && this.account.login
          && this.account.pass
        ) {
          this.tree.splice(0);
          this.isTesting = true;
          this.errorState = false;
          this.currentPage = 2;
          let ok = false;
          if (['imap', 'pop'].includes(this.accountCode)) {
            if (this.account.host
              && bbn.fn.isHostname(this.account.host)
              && (!this.hasSMTP || this.smtp)
            ) {
              ok = true;
            }
          }
          else {
            ok = true;
          }

          if (ok) {
            this.post(
              this.cp.source.root + 'actions/account',
              bbn.fn.extend({action: 'test'}, this.account),
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
                    this.account.folders = checked;
                    const tree = this.getRef('tree');
                    if (tree) {
                      tree.checked = this.account.folders;
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
        if (d && d.success) {
          const idx = bbn.fn.search(d.data, { id: d.id_account})
          this.cp.source.accounts.push(d.data[idx]);
          this.cp.setTreeData();
          this.$nextTick(() => {
            const tree = this.cp.getRef('tree');
            if (tree) {
              tree.updateData().then(() => {
                tree.reload();
              });
            }
          })
        }
      },
    },
    watch: {
      hasSMTP(v){
        if (v && this.account.host) {
          this.account.smtp = this.account.host;
        }
        else {
          this.account.smtp = null;
        }
      },
      "account.email"(nv, ov) {
        if (nv) {
          if (ov === this.account.login) {
            this.account.login = nv;
          }
          else if (!this.account.login) {
            this.account.login = nv;
          }
        }
      }
    }
  }
})();