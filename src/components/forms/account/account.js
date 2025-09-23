(() => {
  return {
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
        }
      }
    },
    computed: {
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
        this.errorState = false;
        this.tree.splice(0, this.tree.length);
        this.account.folders.splice(0, this.account.folders.length);
        this.account.pass = '';
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
      account: {
        deep: true,
        handler(){
          bbn.fn.log("ACCOUNT WATCHER");
          if (this.accountChecker) {
            clearTimeout(this.accountChecker);
          }
          this.accountChecker = setTimeout(() => {
            if (!this.tree.length
                && this.account.email
                && bbn.fn.isEmail(this.account.email)
                && this.account.type
                && this.account.login
                && this.account.pass
               ) {
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
                bbn.fn.post(
                  this.cp.source.root + 'actions/account',
                  bbn.fn.extend({action: 'test'}, this.account),
                  d => {
                    if (d.data) {
                      bbn.fn.log("DATA", d);
                      let checked = [];
                      bbn.fn.each(d.data, a => {
                        if (a.subscribed) {
                          checked.push(a.uid);
                        }
                        this.tree.push(a);
                      });
                      this.errorState = false;
                      this.$nextTick(() => {
                        this.account.folders = checked;
                        this.getRef('tree').checked = this.account.folders;
                        this.getRef('tree')?.updateData();
                      });
                    }
                    else {
                      this.errorState = true;
                    }
                  }
                );
              }
            }
          }, 1000)
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