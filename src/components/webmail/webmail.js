// Javascript Document
(() => {
  let cp;
  let scpName = bbn.fn.randomString().toLowerCase();
  return {
    mixins: [
      bbn.vue.basicComponent
    ],
    data(){
      return {
        scpName: scpName,
        orientation: 'vertical',
        currentFolder: null,
        selectedMail: null,
        treeData: [],
        folders: [],
        foldersData: [],
        moveTo: "",
        root: appui.plugins['appui-email']
      };
    },
    computed: {
      dataObj(){
        return {
          id_folder: this.currentFolder
        }
      },
    },
    methods: {
      onMove(source, dest, event) {
        if (source.data.type !== "folders") {
          event.preventDefault();
        }
        bbn.fn.log(arguments);
        return false;
      },
      treeMenu(node) {
        res = []
        bbn.fn.log("SELECTED TREE ITEM", node.data);
        if (node.data.type === "account") {
          res.push({
            text: bbn._('Delete account'),
            icon: "",
            action: () => {
              this.deleteAccount(node.data.uid)
            }
          })
        }
        if (node.data.type !== "folder_types") {
          res.push({
            text: bbn._('Create folder'),
            icon: "",
            action: () => {
              bbn.fn.log("clicke in", node.data);
              this.getPopup({
                title: bbn._("Folder name"),
                component: "appui-email-forms-create",
                source: {
                  id_account: node.data.type === "account" ? node.data.uid : node.data.id_account,
                  id_parent: node.data.id || null,
                }
              })
            }
          })
        }
        if (node.data.type !== "account" && node.data.type !== "folder_types") {
          res.push({
            text: bbn._('Remove folder'),
            icon: "",
            action: () => {
              this.removeFolder(this.getAllFolderChild(node.data), node.data.text, node.data.id_account);
            }
          },
 					{
						text: bbn._('Rename Folder'),
            icon: "",
            action: () => {
              this.getPopup({
                title: bbn._("Folder new name"),
                component: "appui-email-forms-rename",
                source: {
                 	id_account: node.data.type === "account" ? node.data.uid : node.data.id_account,
                  folders: this.getAllFolderChild(node.data)
                }
              })
            }
          })
        }
        return res;
      },
      getAllFolderChild(folder) {
        res = [];
        res.push({id: folder.id, text: folder.text, id_parent: folder.id_parent || null})
        if (folder.items) {
          for (let idx in folder.items) {
            if (folder.items[idx].items) {
              res = res.concat(this.getAllFolderChild(folder.items[idx]));
            } else {
              res.push({id: folder.items[idx].id, text: folder.items[idx].text, id_parent: folder.items[idx].id_parent || null})
            }
          }
        }
        return res;
      },
      removeFolder(idArray, text, uid) {
        this.confirm(bbn._(`Do you want to delete the ${text} folder and all the subfolders ?`), () => {
          bbn.fn.post(this.root + '/actions/folder/delete', {
            // reverse the array to delete the the last subfolders before
            id: idArray.reverse(),
            id_account: uid
          }, d => {
            let tree = this.getRef('tree');
            let idx = bbn.fn.search(this.source.accounts, { id: uid})
            if (d.success) {
              appui.success(bbn._(`${text} folder and subfolders ar successfuly deleted`));
              this.source.accounts.splice(idx, 1, d.account);
            } else {
              for (let idx in d.res) {
                if (d.res[idx].success) {
                  appui.success(bbn._(`${d.res[idx].text} successfuly deleted`))
                } else {
                  appui.success(bbn._(`${d.res[idx].text} impossible to delete`))
                }
              }
            }
            this.setTreeData();
            tree.updateData().then( () => {
              tree.reload()
            })
          })
        })
      },
      deleteAccount(uid) {
        this.confirm(bbn._("Do you want to delete this account ?"), () => {
          bbn.fn.post(this.root + '/actions/account', {
            action: 'delete',
            data: {
              id: uid
            }
          }, d => {
            if (d.success) {
              appui.success(bbn._("Account deleted with success"));
              let tree = this.getRef('tree');
              let idx = bbn.fn.search(this.source.accounts, { id: uid})
              this.source.accounts.splice(idx, 1)
              this.setTreeData();
              tree.updateData().then( () => {
                tree.reload()
              })
            } else {
              appui.error(bbn._(d.error ? d.error : "Impossible to delete the account"));
            }
          })
        })
      },
      setTreeData(){
        bbn.fn.log("TreeData");
        let r = [];
        let fn = (ar, id_account) => {
          let res = [];
          bbn.fn.log("ar", ar)
          bbn.fn.log("folder_types", this.source.folder_types);
          bbn.fn.each(this.source.folder_types, ft => {
            bbn.fn.each(ar, a => {
              a.type = "folder";
              if (ft.names && ft.names.indexOf(a.uid) > -1) {
                res.push(bbn.fn.extend({
                  id_account: id_account,
                }, a))
              }
            })
          });
          let commonFolder = bbn.fn.getRow(this.source.folder_types, {code: 'folders'});
          bbn.fn.each(ar, a => {
            if (!bbn.fn.getRow(res, {uid: a.uid})) {
              let tmp = bbn.fn.extend({
                id_account: id_account,
              }, a);
              let folder = commonFolder;
              bbn.fn.each(this.source.folder_types, ft => {
                if (ft.names && ft.names.indexOf(a.uid) > -1) {
                  folder = ft;
                  return false;
                }
              });
              tmp.icon = folder.icon;
              if (tmp.items) {
                tmp.items = fn(tmp.items, id_account)
              }
              tmp.type = "folders"
              res.push(tmp);
            }
          })
          return res;
        }
        bbn.fn.each(this.source.folder_types, a => {
          r.push({
            text: a.text,
            uid: a.code,
            names: a.names,
            icon: a.icon,
            id: a.id,
            type: "folder_types"
          });
        })
        if (this.source.accounts) {
          bbn.fn.each(this.source.accounts, a => {
            r.push({
              text: a.login,
              uid: a.id,
              items: fn(a.folders, a.id),
              type: "account"
            });
          });
        }
        this.treeData = r;
      },
      getFolders() {
        bbn.fn.log("ICI", this.selectedMail)
        if (this.selectedMail) {
          this.post('emails/webmail/get_folders', {
            id: this.selectedMail.id_folder,
          }, (d) => {
            if (d.success) {
              this.folders = [];
              this.foldersData = d.data;
              for (let i = 0; i < d.data.length; i++) {
                if (d.data[i].id == this.selectedMail.id_folder) {
                  this.moveTo = d.data[i].text;
                }
                this.folders.push({text: d.data[i].text, value: d.data[i].id})
              }
              bbn.fn.log("Selected mailAccount folders", this.folders);
            }
          })
        }
        return this.folders;
      },
      showAttachments(row){
        if (row.attachments) {
          let attachments = bbn.fn.isString(row.attachments) ? JSON.parse(row.attachments) : row.attachments;
          return attachments.length
        }
        return '-';
      },
      selectFolder(node) {
        this.currentFolder = node.data.id;
      },
      showSubject(row) {
        let st  = row.subject;
        if (!row.is_read) {
          st = '<strong>' + st + '</strong>';
        }
        return st;
      },
      selectMessage(row) {
        this.selectedMail = row;
        this.getFolders();
        bbn.fn.log(row)
      },
      createAccount() {
        this.getPopup({
          width: 500,
          height: 450,
          title: bbn._("eMail account configuration"),
          component: this.$options.components[scpName]
        })
      },
      treeMapper(a) {
        bbn.fn.log(a);
        return {
          text: a.uid
        }
      },
      reply(){
        bbn.fn.link(this.source.root + "webmail/write/reply/" + this.selectedMail.id);
      },
      replyAll(){
        bbn.fn.link(this.source.root + "webmail/write/reply_all/" + this.selectedMail.id);
      },
      forward(){
        bbn.fn.link(this.source.root + "webmail/write/forward/" + this.selectedMail.id);
      },
      writeNewEmail() {
        bbn.fn.link(this.source.root + "webmail/write");
      },
      archive(){
        if (this.selectedMail) {

        }
      },
      setAsJunk(){
        if (this.selectedMail) {

        }
      },
      openTab(){
        bbn.fn.link(this.source.root + "webmail/view/" + this.selectedMail.id);
      },
      openWindow(){
        if (this.selectedMail) {

        }
      },
      deleteMail(){
        this.confirm(bbn._('Do you want to delete this email ?'), () => {
          this.post("emails/" + 'actions/email/delete', {
            id: this.selectedMail.id,
            status: "ready"
          }, d => {
            if (d.success) {
              appui.success(bbn._('Email deleted with success'))
            } else {
              appui.error(bbn._('An error occured when deleting the email'))
            }
          })
        })
      },
      moveFolder() {
        this.getFolders();
        this.getPopup({
          title: bbn._("Folder changer"),
          component: 'appui-email-forms-moveto',
          componentOptions: {
            source: {
              id : this.selectedMail.id,
              folders: this.folders,
              foldersData: this.foldersData,
            }
          }
        })
      },
      submitFolderChange() {
        bbn.fn.log("FOLDER", this.moveTo);
      },
      mailToTask(){
        if (this.selectedMail) {

        }
      }
    },
    watch: {
      currentFolder(){
        this.$forceUpdate();
        this.$nextTick(() => {
          bbn.fn.log("currentFolderWtacher");
          this.getRef('table').updateData()
        })
      }
    },
    created(){
      cp = this;
      this.setTreeData();
    },
    components: {
      [scpName]: {
        template: '#' + scpName + '-editor',
        data(){
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
              email: ''
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
              let tree = cp.getRef('tree');
              let idx = bbn.fn.search(d.data, { id: d.id_account})
              bbn.fn.log("data", d.data[idx])
              cp.source.accounts.push(d.data[idx]);
              cp.setTreeData();
              tree.updateData().then( () => {
                tree.reload()
              })
            }
          }
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
                            this.getRef('tree').updateData();
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
    }
  };
})()