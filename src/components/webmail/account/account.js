(() => {
  const rules = ['inbox', 'drafts', 'sent', 'spam', 'trash', 'archive'];
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        default(){
          return {
            folders: [],
            rules: rules.reduce((a, v) => ({...a, [v]: ''}), {}),
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
      },
      folderTypes: {
        type: Array,
        required: true
      }
    },
    data(){
      return {
        root: appui.plugins['appui-email'] + '/',
        tree: [],
        errorState: false,
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
      accountCode(){
        if (this.source.type) {
          return bbn.fn.getField(this.types, 'code', {id: this.source.type});
        }
        return null;
      },
      treeSource(){
        return bbn.fn.order(this.tree, 'text', 'ASC');
      },
      totalFolders(){
        let tot = 0;
        if (this.tree?.length) {
          const countItems = (items) => {
            bbn.fn.each(items, a => {
              tot++;
              if (a.items?.length) {
                countItems(a.items);
              }
            })
          };
          countItems(this.tree);
        }

        return tot;
      },
      isAllChecked(){
        return this.tree?.length && (this.source?.folders?.length === this.totalFolders);
      },
      isIntermediateChecked(){
        return this.source?.folders?.length && (this.source?.folders?.length < this.totalFolders);
      }
    },
    methods: {
      backToConfig(){
        this.tree.splice(0);
        this.source.folders.splice(0);
        this.errorState = false;
        this.isTesting = false;
        this.currentPage = 1;
      },
      nextToTest(ev){
        if (ev?.type === 'submit') {
          ev.preventDefault();
        }

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

                    a.icon = "nf nf-md-folder";
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

                    bbn.fn.each(rules, r => {
                      if (!this.source.rules[r].length) {
                        const af = this.availableFolders(r) || [];
                        const t = bbn.fn.getField(this.folderTypes, 'id', {code: r});
                        if (t) {
                          this.source.rules[r] = bbn.fn.getField(af, 'value', {id_option: t}) || '';
                        }
                      }
                    });
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
      checkUncheckAll(){
        if (this.isAllChecked) {
          this.source.folders.splice(0);
        }
        else {
          const addToChecked  = (items) => {
            bbn.fn.each(items, a => {
              if (!this.source.folders.includes(a.uid)) {
                this.source.folders.push(a.uid);
              }

              if (a.items?.length) {
                addToChecked(a.items);
              }
            })
          };
          addToChecked(this.tree);
        }
      },
      availableFolders(role){
        return bbn.fn.order(
          bbn.fn.map(
            bbn.fn.filter(
              this.tree || [],
              f => {
                const rules = bbn.fn.clone(this.source.rules);
                delete rules[role];
                return !Object.values(rules).includes(f.uid)
              }
            ),
            f => {
              /* const bits = f.uid.split('.');
              let o;
              let t = '';
              let u = '';
              bbn.fn.each(bits, (b, i) => {
                o = bbn.fn.getRow(o ? o.items : this.tree, {uid: u.length ? u + b : b});
                t += o.text;
                u += b;
                if (bits[i + 1]) {
                  t += ' > ';
                  u += '.';
                }

              });
              return {
                text: t,
                value: o.uid,
                icon: o.icon,
                id_option: o.id_option
              } */
              return {
                text: f.text,
                value: f.uid,
                icon: f.icon,
                id_option: f.id_option
              }
            }
          ),
          'text'
        );
      }
    },
    beforeMount(){
      if (this.source.rules) {
        bbn.fn.each(rules, r => {
          if ((this.source.rules[r] === undefined)) {
            this.source.rules[r] = '';
          }
        });
      }
    },
    watch: {
      "source.host"(nv, ov){
        if (nv) {
          if (ov === this.source.smtp) {
            this.source.smtp = nv;
          }
          else if (!this.source.smtp) {
            this.source.smtp = nv;
          }
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