(() => {
  const rules = ['inbox', 'drafts', 'sent', 'spam', 'trash', 'archive'];
  const defaultSource = {
    folders: [],
    rules: rules.reduce((a, v) => ({...a, [v]: ''}), {}),
    type: null,
    email: '',
    login: '',
    pass: '',
    host: '',
    encryption: 1,
    port: 993,
    validatecert: 1,
    smtp: '',
    locale: true
  };
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        default(){
          return bbn.fn.clone(defaultSource);
        }
      },
      types: {
        type: Array,
        required: true
      },
      folderTypes: {
        type: Array,
        required: true
      },
      smtps: {
        type: Array,
        default(){
          return [];
        }
      }
    },
    data(){
      return {
        root: appui.plugins['appui-email'] + '/',
        tree: [],
        errorState: false,
        errorMessage: null,
        currentPage: 1,
        isTesting: false,
        isDev: appui.user.isDev,
        isTreeLoaded: false
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
            });
            if (!this.errorState) {
              btns.push('submit')
            }

            break;
        }

        return btns;
      },
      typeCode(){
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
        bbn.fn.iterate(this.source.rules, (v, k) => this.source.rules[k] = '');
        this.errorState = false;
        this.errorMessage = null;
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
          this.errorMessage = null;
          this.currentPage = 2;
          let ok = false;
          if (this.typeCode === 'imap') {
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
                      tree.$once('dataloaded', () => {
                        this.isTreeLoaded = true;
                      });
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
                  this.errorMessage = d.error || null;
                  this.isTesting = false;
                }
              },
              d => {
                this.errorState = true;
                this.errorMessage = d.error || null;
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
        const tree = this.getRef('tree');
        if (!tree) {
          return [];
        }

        return bbn.fn.order(
          bbn.fn.map(
            bbn.fn.filter(
              this.tree || [],
              f => {
                const rules = bbn.fn.clone(this.source.rules);
                delete rules[role];
                return !Object.values(rules).includes(f.uid) && tree.checked.includes(f.uid);
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
      },
      addSmtp(){
        this.getPopup({
          label: bbn._('Add SMTP server'),
          component: 'appui-email-webmail-smtp',
          componentOptions: {
            locale: !!this.source.locale
          },
          componentEvents: {
            success: (d) => {
              if (d.success && d.data?.id) {
                this.smtps.push(d.data);
                this.source.smtp = d.data.id;
                appui.success(bbn._('The SMTP server has been added'));
              }
              else {
                appui.error(d.error || bbn._('Cannot add the SMTP server'));
              }
            },
            failure: (d) => {
              appui.error(d.error || bbn._('Cannot add the SMTP server'));
            }
          }
        });
      },
      editSmtp(idSmtp){
        if (idSmtp) {
          const smtp = bbn.fn.getRow(this.smtps, {id: idSmtp});
          if (smtp) {
            this.getPopup({
              label: bbn._('Edit SMTP server'),
              component: 'appui-email-webmail-smtp',
              componentOptions: {
                source: bbn.fn.clone(smtp)
              },
              componentEvents: {
                success: (d) => {
                  if (d.success && d.data) {
                    const index = bbn.fn.search(this.smtps, {id: idSmtp});
                    if (index !== -1) {
                      this.smtps.splice(index, 1, d.data);
                    }

                    appui.success(bbn._('The SMTP server has been updated'));
                  }
                },
                failure: (d) => {
                  appui.error(d.error || bbn._('Cannot add the SMTP server'));
                }
              }
            });
          }
        }
      },
      resetSource(){
        const newSource = bbn.fn.clone(defaultSource);
        bbn.fn.each(newSource, (v, k) => {
          if (k !== 'type') {
            this.source[k] = v;
          }
        });
      },
      onTreeCheck(a,b,c){
        bbn.fn.log('mikko',a,b,c);
      },
      onTreeUncheck(a,b,c){
        bbn.fn.log('mikko2',a,b,c);
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
      'source.type'(){
        this.resetSource();
      },
      'source.email'(nv, ov) {
        if (nv) {
          if (ov === this.source.login) {
            this.source.login = nv;
          }
          else if (!this.source.login) {
            this.source.login = nv;
          }
        }
      },
      'source.encryption'(newVal){
        if (!newVal) {
          this.source.validatecert = 0;
          if (this.source.port === 993) {
            this.source.port = 143;
          }
        }
        else if (this.source.port === 143) {
          this.source.port = 993;
        }
      }
    }
  }
})();