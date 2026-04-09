// Javascript Document
(() => {
  const slots = bbn.fn.createObject();
  return {
    mixins: [
      bbn.cp.mixins.basic
    ],
    data(){
      return {
        orientation: 'horizontal',
        currentAccount: null,
        currentFolder: null,
        selectedMail: null,
        selectedMails: [],
        treeData: [],
        folders: [],
        foldersData: [],
        alreadySendUpdateError: false,
        moveTo: "",
        root: appui.plugins['appui-email'] + '/',
        newCount: 0,
        hash: this.source.hash,
        sync: null,
        extractedFrom: null,
        extractedTo: null,
        selectedMode: "download",
        syncId: false,
        syncMessage: '',
        threads: true,
        accountsIdle: {},
        currentSearch: '',
        currentSearchObj: {
          logic: 'OR',
          conditions: []
        },
        isSearching: false,
        pluginsSlots: slots,
        priorityList: [{
          value: 1,
          text: bbn._('Highest'),
          color: 'red',
          cls: 'bbn-red'
        }, {
          value: 2,
          text: bbn._('High'),
          color: 'orange',
          cls: 'bbn-orange'
        }, {
          value: 3,
          text: bbn._('Normal'),
          color: 'green',
          cls: 'bbn-green'
        }, {
          value: 4,
          text: bbn._('Low'),
          color: 'blue',
          cls: 'bbn-blue'
        }, {
          value: 5,
          text: bbn._('Lowest'),
          color: 'grey',
          cls: 'bbn-grey'
        }],
        treeSource: [],
        itemEvents: {
          select: item => this.selectMail(item)
        }
      }
    },
    computed: {
      dataObj(){
        let idFolder = this.currentFolder;
        if (this.currentAccount && !idFolder) {
          idFolder = bbn.fn.map(
            bbn.fn.getField(
              this.source.accounts,
              'folders',
              {id: this.currentAccount}
            ),
            f => f.id
          );
        }

        return {
          id_folder: idFolder,
          threads: this.threads
        }
      },
      currentFolderObj(){
        if (this.currentAccount && this.currentFolder) {
          const account = bbn.fn.getRow(this.source.accounts, {id: this.currentAccount});
          if (account) {
            return this.getFolder(this.currentFolder, account.folders) || false;
          }
        }
        else if (this.currentAccount) {
          const account = bbn.fn.getRow(this.source.accounts, {id: this.currentAccount});
          const obj = {
            num_msg: 0,
            db_num_msg: 0
          };
          bbn.fn.each(account.folders, f => {
            obj.num_msg += f.num_msg;
            obj.db_num_msg += f.db_num_msg;
          });
        }
        else if (this.currentFolder) {
          const type = bbn.fn.getField(this.source.folder_types, 'code', {id: this.currentFolder});
          if (type) {
            const obj = {
              num_msg: 0,
              db_num_msg: 0
            };
            bbn.fn.each(this.source.accounts, a => {
              bbn.fn.each(a.folders, f => {
                if (f.type === type) {
                  obj.num_msg += f.num_msg;
                  obj.db_num_msg += f.db_num_msg;
                }
              });
            });
            return obj;
          }
        }

        return false;
      },
      currentAccountObj(){
        if (this.currentAccount) {
          return bbn.fn.getRow(this.source.accounts, {id: this.currentAccount}) || false;
        }

        return false;
      }
    },
    methods: {
      formatDate(date) {
        const emailDate = bbn.dt(date);
        const currentDate = bbn.dt();
        if (emailDate.year() !== currentDate.year()) {
          return bbn.dt(date).format("lll");
        }
        else if (emailDate.format('DDMMYYYY') === currentDate.format('DDMMYYYY')) {
          return bbn.dt(date).format("LT");
        }
        else {
          return bbn.dt(date).format("lll");
        }
      },
      changeOrientation() {
        this.orientation = this.orientation == 'vertical' ? 'horizontal' : 'vertical';
      },
      currentFolderIsThreads() {
        return this.currentFolder.startWiths('threads-')
      },
      hasExpander(row){
        return !!row.data?.external_uids
      },
      expanderComponent(row){
        return {
          template: `<component is="appui-email-widget-table" :source="source"/>`,
          props: ['source']
        }
      },
      receive(d) {
        if (d.sync) {
          this.sync = d.sync;
        }
        d = d.hashes;
        let tree = this.getRef('tree');
        if (!tree)
          return;
        if (JSON.stringify(d) !== JSON.stringify(this.hash)) {
          // if a key it's not in this.hash but in d it's because a new account was added and if the key it's not in d but in this.hash it's because an account was deleted
          let addedAccount = [];
          let removedAccount = [];
          for (const key in d) {
            if (!this.hash[key] && bbn.fn.search(this.source.accounts, { id: key}) < 0) {
              addedAccount.push(key);
            }
          }
          for (const key in this.hash) {
            if (!d[key] && bbn.fn.search(this.source.accounts, { id: key}) >= 0) {
              removedAccount.push(key)
            }
          }
          if (addedAccount.length) {
            for (const accountId of addedAccount) {
              if (bbn.fn.search(this.source.accounts, { id: accountId}) < 0) {
                bbn.fn.post(this.root + 'webmail/actions/account', {
                  action: 'get',
                  id: accountId
                } , d => {
                  if (d.account) {
                    this.source.accounts.push(d.account);
                    this.updateTree();
                  }
                });
              }
            }
          }
          if (removedAccount.length) {
            // remove account
            for (const accountId of removedAccount) {
              let idx = bbn.fn.search(this.source.accounts, { id: accountId})
              this.source.accounts.splice(idx, 1)
            }
          }

          for (const key in d) {
            // if the hash is not the same it's because a folder have changed so we perfom that changed
            if (this.hash[key] && d[key].hash !== this.hash[key].hash) {
              if (d[key].folders[this.currentFolder] && d[key].folders[this.currentFolder] !== this.hash[key].folders[this.currentFolder]) {
                if (!this.selectedMails.length) {
                  this.reloadMailList();
                  if (this.alreadySendUpdateError) {
                    this.alreadySendUpdateError = false;
                  }
                } else if (!this.alreadySendUpdateError) {
                  appui.error(bbn._('Due to your selections this folder cannot be updated automatically as long as you have selected emails'));
                  this.alreadySendUpdateError = true;
                }
              }
              // iterate each hash of each folder to see what folder have changed
              if (bbn.fn.search(this.source.accounts, { id: key}) !== -1) {
                bbn.fn.post(this.root + 'webmail/actions/account', {
                  action: 'get',
                  id: key
                } , d => {
                  if (d.account) {
                    let idx = bbn.fn.search(this.source.accounts, {id: d.account.id});
                    if (idx >= 0) {
                      this.source.accounts[idx] = d.account;
                      this.updateTree();
                    }
                  }
                });
              }
            }
          }
          this.$nextTick(() => {
            this.updateTree();
            this.getRef('tree').updateData(false, false);
          });
          this.hash = d;
          this.$set(appui.pollerObject, 'appui-email', {
            hashes: this.hash
          });
          appui.poll();
        }
      },
      onMoveStart(source, event) {
        if (source.data.type === 'account') {
          event.preventDefault();
        }
      },
      onMove(source, dest, event) {
        if (source.data.type === 'account') {
          event.preventDefault();
          return false;
        }

        if ((dest.data.type !== 'account')
          && (source.data.id_account !== dest.data.id_account)
        ) {
          event.preventDefault();
          return false;
        }

        if ((dest.data.type === 'account')
          && (source.data.id_account !== dest.data.uid)
        ) {
          event.preventDefault();
          return false;
        }

        if (source.data.type !== 'folders') {
          event.preventDefault();
          return false;
        }

        this.post(this.root + 'webmail/actions/folder/move', {
          to: dest.data.id,
          id_account: source.data.id_account,
          folders: this.getAllFolderChild(source.data)
        }, d => {
          const accountIdx = bbn.fn.search(this.source.accounts, {id: source.data.id});
          if (d.success) {
            appui.success(bbn._("%s folder and subfolders ar successfuly moved to %s", source.data.text, dest.data.text));
            this.source.accounts.splice(accountIdx, 1, d.account);
          }
          else if (d.failed?.length) {
            appui.eror(bbn._("Impossibile to move the folders:<br>%s", bbn.fn.map(d.failed, f => f.text).join('<br>')));
          }
          else {
            appui.eror(bbn._("Impossibile to move the %s folder into %s folder", source.data.text, dest.data.text));
          }

          if (d.success || d.failed?.length) {
            this.$nextTick(() => {
              this.updateTree();
            });
          }
        });
        event.preventDefault();
        return true;
      },
      treeMenu(node) {
        const res = []
        if (node.data.type !== "folder_types") {
          res.push({
            text: bbn._('Create folder'),
            icon: "nf nf-fa-plus",
            action: () => {
              const componentOptions = {
                idAccount: node.data.type === "account" ? node.data.uid : node.data.id_account
              };
              if (node.data.type !== "account") {
                componentOptions.idParent = node.data.id;
              }

              this.getPopup({
                label: bbn._("Create new folder"),
                component: "appui-email-webmail-folder-create",
                componentOptions,
                componentEvents: {
                  success: d => {
                    if (d.success) {
                      const acc = bbn.fn.getRow(this.source.accounts , {id: d.account.id});
                      if (acc) {
                        acc.folders.splice(0, acc.folders.length, ...d.account.folders);
                      }

                      this.$nextTick(() => {
                        this.updateTree();
                        appui.success(bbn._("Folder created with success"));
                      });
                    }
                    else {
                      appui.error(d.error ? d.error : bbn._("Impossible to create the folder"));
                    }
                  }
                }
              })
              appui.poll();
            }
          })
        }

        if (node.data.type === 'folders') {
          res.push({
            text: bbn._('Remove folder'),
            icon: "nf nf-md-folder_remove",
            action: () => {
              this.removeFolder(node.data.id, node.data.id_account, node.data.text);
              appui.poll();
            }
          });
        }

        if (!['account', 'folder_types'].includes(node.data.type)) {
          res.push({
            text: bbn._('Rename folder'),
            icon: "nf nf-md-rename_box",
            action: () => {
              this.getPopup({
                label: bbn._("Rename folder"),
                component: "appui-email-webmail-folder-rename",
                componentOptions: {
                  idAccount: node.data.id_account,
                  idFolder: node.data.id,
                  name: node.data.text,
                  folders: bbn.fn.map(
                    bbn.fn.filter(
                      bbn.fn.clone(node.parent.source),
                      s => s.type === 'folder_types'
                    ),
                    f => f.text
                  )
                },
                componentEvents: {
                  success: d => {
                    if (d.success) {
                      const acc = bbn.fn.getRow(this.source.accounts , {id: d.account.id});
                      if (acc) {
                        acc.folders.splice(0, acc.folders.length, ...d.account.folders);
                      }

                      this.$nextTick(() => {
                        this.updateTree();
                        appui.success(bbn._("Folder renamed with success"));
                      });
                    }
                    else {
                      appui.error(d.error ? d.error : bbn._("Impossible to rename the folder"));
                    }
                  }
                }
              })
              appui.poll();
            }
          })
        }

        res.push({
          text: node.data.type === 'account' ? bbn._('Synchronize account') : bbn._('Synchronize folder'),
          icon: "nf nf-oct-sync",
          action: () => {
            switch (node.data.type) {
              case "account":
                this.synchronize(node.data.id, false);
                break;
              case "folder_types":
                this.synchronize(false, false);
                break;
              default:
                this.synchronize(node.data.id_account, node.data.id);
                break;
            }
          },
          disabled: !!this.syncId
        });

        if (node.data.type === "account") {
          res.push({
            text: bbn._("Account settings"),
            icon: "nf nf-seti-settings",
            action: () => {
              this.editAccount(node.data.id);
            },
            disabled: true
          }, {
            text: bbn._('Delete account'),
            icon: "nf nf-md-delete",
            action: () => {
              this.deleteAccount(node.data.id)
              appui.poll();
            }
          })
        }

        return res;
      },
      getAllFolderChild(folder, onlyId = false) {
        const res = [];
        res.push(onlyId ? folder.id : {
          id: folder.id,
          text: folder.text,
          id_parent: folder.id_parent || null
        })
        if (folder.items) {
          bbn.fn.each(folder.items, i => {
            if (i.items?.length) {
              res = res.concat(this.getAllFolderChild(i, onlyId));
            }
            else {
              res.push(onlyId ? i.id : {
                id: i.id,
                text: i.text,
                id_parent: i.id_parent || null
              });
            }
          });
        }
        return res;
      },
      removeFolder(idFolder, idAccount, folderTitle) {
        this.confirm(bbn._(`Do you want to delete the "${folderTitle}" folder and all the subfolders ?`), () => {
          this.post(this.root + 'webmail/actions/folder/delete', {
            id: idFolder,
            id_account: idAccount
          }, d => {
            const idx = bbn.fn.search(this.source.accounts, {id: idAccount})
            if (d.success) {
              appui.success(bbn._(`${folderTitle} folder and subfolders ar successfuly deleted`));
              this.source.accounts.splice(idx, 1, d.account);
              this.$nextTick(() => {
                this.updateTree();
              })
            }
            else {
              appui.error(bbn._("Impossible to delete the folder"));
            }
          })
        })
      },
      deleteAccount(id) {
        this.confirm(bbn._("Do you want to delete this account ?"), () => {
          bbn.fn.post(this.root + 'webmail/actions/account', {
            action: 'delete',
            id
          }, d => {
            if (d.success) {
              appui.success(bbn._("Account deleted with success"));
              const idx = bbn.fn.search(this.source.accounts, {id})
              this.source.accounts.splice(idx, 1)
              this.$nextTick(() => {
                this.updateTree();
              });
            }
            else {
              appui.error(d.error ? d.error : bbn._("Impossible to delete the account"));
            }
          })
        })
      },
      getFolder(idFolder, folders) {
        let res = null;
        if (folders === undefined) {
          bbn.fn.each(this.source.accounts, a => {
            const f = this.getFolder(idFolder, a.folders);
            if (f) {
              res = f;
              return false;
            }
          });
        }
        else if (bbn.fn.isArray(folders)) {
          bbn.fn.each(folders, f => {
            if (f.id === idFolder) {
              res = f;
              return false;
            }
            else if (f.items?.length) {
              const sf = this.getFolder(idFolder, f.items);
              if (sf) {
                res = sf;
                return false;
              }
            }
          });
        }

        return res;
      },
      getFolders() {
        if (this.selectedMail) {
          this.post(this.root + 'webmail/data/folders', {
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
            }
          })
        }
        return this.folders;
      },
      selectFolder(node) {
        this.selectedMail = null;
        this.selectedMails = [];
        switch (node.data.type) {
          case 'account':
            this.currentFolder = null;
            this.currentAccount = node.data.id;
            break;
          case 'folder_types':
            this.currentFolder = node.data.id;
            this.currentAccount = null;
            break;
          default:
            if (bbn.fn.getRow(this.source.folder_types, 'code', node.data.type)) {
              this.currentFolder = node.data.id;
              this.currentAccount = node.data.id_account;
            }

            break;
        }
      },
      showSubject(row) {
        let st  = row.subject;
        if (!row.is_read) {
          st = '<strong>' + st + '</strong>';
        }
        return st;
      },
      selectMail(item) {
        const idx = this.selectedMails.indexOf(item.id);
        if (idx === -1) {
          this.selectedMails.push(item.id);
        }
        else if (this.selectedMail?.id !== item.id) {
          this.selectedMails.splice(idx, 1)
        }

        if (!this.selectedMail || this.selectedMail.id !== item.id) {
          this.selectedMail = null;
          this.$nextTick(() => {
            setTimeout(() => {
              item.is_read = 1;
              this.selectedMail = item;
              this.getFolders();
            }, 100);
          });
        }
      },
      selectedMessageIDisSame(id) {
        if (this.selectedMail === null) {
          return false;
        }
        return this.selectedMail.id === id;
      },
      createAccount(){
        this.editAccount(null);
        appui.poll();
      },
      editAccount(idAccount){
        const componentOptions = {
          types: this.source.types,
          folderTypes: this.source.folder_types,
          smtps: this.source.smtps
        };
        if (idAccount && bbn.fn.isString(idAccount)) {
          componentOptions.source = bbn.fn.clone(bbn.fn.getRow(this.source.accounts, {id: idAccount}));
        }

        this.getPopup({
          label: bbn._("Account configuration"),
          component: 'appui-email-webmail-account',
          componentOptions,
          componentEvents: {
            success: d => {
              if (d?.success && d?.data?.id) {
                const index = bbn.fn.search(this.source.accounts, {id: d.data.id});
                if (index > -1) {
                  this.source.accounts.splice(index, 1, d.data);
                }
                else {
                  this.source.accounts.push(d.data);
                }

                this.$nextTick(() => {
                  this.updateTree();
                })
              }
              else {
                appui.error(d.error?.length ? d.error : bbn._("An error occurred while saving the account"));
              }
            },
            failure: d => {
              appui.error(d.error?.length ? d.error : bbn._("An error occurred while saving the account"));
            }
          }
        });
      },
      treeMapper(a) {
        return {
          text: a.uid
        }
      },
      writeNewEmail() {
        this.newCount++;
        bbn.fn.link(this.root + "webmail/write/new/" + this.newCount);
      },
      submitFolderChange() {
        bbn.fn.log("FOLDER", this.moveTo);
      },
      onSyncClick(){
        this.synchronize(this.currentAccount, this.currentFolder);
      },
      synchronize(idAccount, idFolder){
        if (!this.syncId) {
          const url = this.root + 'webmail/actions/sync';
          const data = {
            id_account: bbn.fn.isString(idAccount) ? idAccount : false,
            id_folder: bbn.fn.isString(idFolder) ? idFolder : false
          };
          let numMsg = 0;
          const fromItems = function(items) {
            bbn.fn.each(items, f => {
              numMsg += Math.abs(f.num_msg - f.db_num_msg);
              if (f.items?.length) {
                fromItems(f.items);
              }
            });
          };
          if (data.id_folder && data.id_account) {
            const acc = bbn.fn.getRow(this.source.accounts, {id: data.id_account});
            if (acc) {
              const folder = bbn.fn.getRow(acc.folders, {id: data.id_folder});
              if (folder) {
                numMsg = Math.abs(folder.num_msg - folder.db_num_msg);
              }
            }
          }
          else if (data.id_account) {
            const acc = bbn.fn.getRow(this.source.accounts, {id: data.id_account});
            if (acc) {
              fromItems(acc.folders);
            }
          }
          else {
            bbn.fn.each(this.source.accounts, a => {
              fromItems(a.folders);
            });
          }

          this.syncId = bbn.fn.getRequestId(url, data);
          this.syncMessage = '<span>' + bbn._('Synchronizing %d/%d', 0, numMsg) + '</span>';
          bbn.fn.stream(
            url,
            d => {
              if (!d) {
                bbn.fn.log('stream', d);
              }
              if (!d.success && d.data?.error) {
                bbn.fn.warning(d.data.error);
                appui.error(bbn._('Failed synchronization'));
                this.syncMessage = '<span class="bbn-red">' + bbn._('Failed synchronization') + '</span>';
                setTimeout(() => {
                  this.syncId = false;
                }, 3000);
              }
              else if (d.isSynchronizing) {
                this.syncMessage = '<span>' + bbn._('Synchronizing %d/%d', d.synchronized, numMsg) + '</span>';
              }
              else if (d.success) {
                this.syncMessage = '<span class="bbn-green">' + bbn._('Synchronization successful') + '</span>';
                if (this.currentFolder
                  && (this.currentFolder === data.id_folder)
                ) {
                  this.reloadMailList();
                }

                setTimeout(() => {
                  this.syncId = false;
                }, 3000);
              }
            },
            data,
            f => {
              appui.error(bbn._('An error occured during the synchronization'));
            },
            a => {
              bbn.fn.log('abort', a);
              this.syncId = false;
            }
          )
        }
      },
      abortSync(){
        if (this.syncId) {
          bbn.fn.abort(this.syncId);
          this.syncId = false;
        }
      },
      getAccountByFolder(idFolder){
        const folder = this.getFolder(idFolder);
        if (folder) {
          return bbn.fn.getRow(this.source.accounts, {id: folder.id_account});
        }

        return null;
      },
      getAccountIdByFolder(idFolder){
        return this.getAccountByFolder(idFolder)?.id || null;
      },
      showAttachments(row){
        if (row.attachments) {
          let attachments = bbn.fn.isString(row.attachments) ? JSON.parse(row.attachments) : row.attachments;
          return attachments.length
        }
        return '-';
      },
      startAccountIdle(idAccount){
        if (!this.accountsIdle[idAccount]) {
          const url = this.root + 'webmail/idle';
          const data = {account: idAccount};
          this.accountsIdle[idAccount] = {
            id: bbn.fn.getRequestId(url, data, 'json'),
            connected: false,
            stream: bbn.fn.stream(
              url,
              d => {
                if (d.success) {
                  delete this.accountsIdle[idAccount];
                }
                else if (d.action) {
                  bbn.fn.log(['IDLE action "' + d.action + '" on account ' + idAccount, d.data]);
                  switch (d.action) {
                    case 'idleStarted':
                      this.accountsIdle[idAccount].connected = true;
                      const treeAccount = bbn.fn.getRow(this.treeSource, {id: idAccount});
                      if (treeAccount) {
                        treeAccount.connected = true;
                      }

                      break;
                    case 'newMail':
                      if (d.data?.folder?.id
                        && d.data.folder?.id_account
                      ) {
                        this.updateFolder(d.data.folder.id_account, d.data.folder.id, d.data.folder).then(() => {
                          appui.info(bbn._('New email received'));
                        });
                      }

                      break;
                    case 'mailDeleted':
                    case 'mailFlagged':
                    case 'syncSubscribedFolders':
                      if (d.data?.folder?.id
                        && d.data.folder?.id_account
                      ) {
                        this.updateFolder(d.data.folder.id_account, d.data.folder.id, d.data.folder);
                      }

                      break;

                  }
                }
                else if (d.error) {
                  appui.error(d.error);
                  this.stopAccountIdle(idAccount);
                }
              },
              data,
              () => this.stopAccountIdle(idAccount, false),
              () => this.stopAccountIdle(idAccount, false),
              () => this.stopAccountIdle(idAccount, false)
            )
          };
        }
      },
      stopAccountIdle(idAccount, abort = true){
        if (this.accountsIdle[idAccount]) {
          if (abort) {
            try {
              bbn.fn.abort(this.accountsIdle[idAccount].id);
              this.accountsIdle[idAccount].aborter.abort();
              bbn.fn._deleteLoader(this.accountsIdle[idAccount].id, {account: idAccount}, true)
            }
            catch (e) {}
          }

          delete this.accountsIdle[idAccount];
        }
      },
      reloadMailList(){
        return new Promise(resolve => {
          const list = this.getRef('mailList');
          if (list?.updateData) {
            list.updateData().then(() => resolve());
          }
          else {
            resolve();
          }
        });
      },
      reloadMailListBackground(){
        return new Promise(resolve => {
          const list = this.getRef('mailList');
          if (list?.updateDataBackground) {
            list.updateDataBackground().then(() => resolve());
          }
          else {
            resolve();
          }
        });
      },
      onSearchKeydown(ev){
        switch (ev.key) {
          case 'Enter':
            this.search();
            break;
          case 'Escape':
            this.searchClear();
            break;
        }
      },
      search(){
        if (this.currentSearch) {
          this.isSearching = true;
          this.currentSearchObj.conditions = [
            {
              field: 'subject',
              operator: 'contains',
              value: this.currentSearch
            },
            {
              field: 'excerpt',
              operator: 'contains',
              value: this.currentSearch
            },
            {
              field: 'from',
              operator: 'contains',
              value: this.currentSearch
            },
            {
              field: 'toname.name',
              operator: 'contains',
              value: this.currentSearch
            },
            {
              field: 'tolink.value',
              operator: 'contains',
              value: this.currentSearch
            }
          ];
        }
      },
      searchClear(){
        this.isSearching = false;
        this.currentSearch = '';
        if (this.currentSearchObj.conditions.length) {
          this.currentSearchObj.conditions = [];
        }
      },
      updateTree(){
        const r = [];
        bbn.fn.each(this.source.folder_types, a => {
          r.push({
            id: a.id,
            uid: a.code,
            text: a.text,
            icon: a.icon,
            type: 'folder_types',
            originalData: a
          });
        })
        if (this.source.accounts?.length) {
          bbn.fn.each(this.source.accounts, a => {
            r.push(this.normalizeAccount(a));
          });
        }

        if (!this.treeSource.length) {
          this.treeSource = r;
        }
        else {
          const syncNodeProps = (target, source) => {
            bbn.fn.iterate(source, (val, k) => {
              if (k !== 'items') {
                target[k] = val;
              }
            });
          };
          const syncLevel = (targetArr, sourceArr) => {
            if (!bbn.fn.isArray(targetArr)) {
              return;
            }

            sourceArr = bbn.fn.isArray(sourceArr) ? sourceArr : [];
            const nodeById = Object.fromEntries(targetArr.map(b => [b.id, b]));
            const findIndexFrom = (arr, id, start) => {
              for (let j = start; j < arr.length; j++) {
                if (arr[j].id === id) {
                  return j;
                }
              }
              return -1;
            };

            let i = 0;
            for (const s of sourceArr) {
              let t = nodeById[s.id] || null;
              if (!t) {
                t = {};
                syncNodeProps(t, s);

                if (bbn.fn.isArray(s.items)) {
                  t.items = [];
                  syncLevel(t.items, s.items);
                }
                else {
                  t.items = [];
                }

                targetArr.splice(i, 0, t);
                nodeById[s.id] = t;
                i++;
                continue;
              }

              if (targetArr[i] !== t) {
                const currIdx = findIndexFrom(targetArr, id, i + 1);
                if (currIdx > -1) {
                  targetArr.splice(currIdx, 1);
                  targetArr.splice(i, 0, t);
                }
                else {
                  targetArr.splice(i, 0, t);
                }
              }

              syncNodeProps(t, s);
              if (bbn.fn.isArray(s.items)) {
                if (!bbn.fn.isArray(t.items)) {
                  t.items = [];
                }
                syncLevel(t.items, s.items);
              }
              else if (bbn.fn.isArray(t.items) && t.items.length) {
                t.items.splice(0, t.items.length);
              }
              else if (!bbn.fn.isArray(t.items)) {
                t.items = [];
              }

              i++;
            }

            if (targetArr.length > i) {
              targetArr.splice(i, targetArr.length - i);
            }
          };

          syncLevel(this.treeSource, r);
        }
      },
      normalizeAccount(account){
        return {
          id: account.id,
          uid: account.id,
          text: account.text || account.email,
          type: 'account',
          items: bbn.fn.map(account.folders, f => this.normalizeFolder(f)),
          originalData: account,
          connected: !!this.accountsIdle[account.id]?.connected,
          cls: 'bbn-top-sspace'
        };
      },
      normalizeFolder(folder){
        const countUnseen = f => {
          let s = f.db_num_unseen_msg || 0;
          if (f?.items?.length) {
            bbn.fn.each(f.items, i => {
              s += countUnseen(i);
            });
          }
          return s;
        };
        return {
          id_account: folder.id_account,
          id: folder.id,
          id_parent: folder.id_parent || null,
          uid: folder.uid,
          text: folder.text,
          icon: folder.icon,
          //type: folder.type !== 'folders' ? 'folder' : 'folders',
          type: folder.type,
          items: bbn.fn.map(folder.items || [], f => this.normalizeFolder(f)),
          originalData: folder,
          unseen: countUnseen(folder),
        }
      },
      updateFolder(idAccount, idFolder, folderData){
        return new Promise(resolve => {
          if (idAccount?.length
            && idFolder?.length
            && (!folderData?.id || (folderData.id === idFolder))
            && (!folderData?.id_account || (folderData.id_account === idAccount))
            && this.source.accounts?.length
          ) {
            const account = bbn.fn.getRow(this.source.accounts, {id: idAccount});
            if (account) {
              const folder = this.getFolder(idFolder, account.folders);
              if (folder) {
                bbn.fn.iterate(folderData, (val, key) => folder[key] = val);
                this.updateTree();
              }
            }

            if (this.currentFolder === idFolder) {
              this.reloadMailListBackground().then(() => resolve());
            }
            else {
              resolve();
            }
          }
        });
      }
    },
    beforeCreate() {
      if (this.source.slots) {
        bbn.fn.iterate(this.source.slots, (slot, comp) => {
          slots[comp] = {};
          bbn.fn.iterate(slot, (arr, s) => {
            slots[comp][s] = [];
            bbn.fn.iterate(arr, a => {
              try {
                let tmp = eval(a.script);
                if (bbn.fn.isObject(tmp)) {
                  if (a.content) {
                    tmp.template = a.content;
                  }
                  slots[comp][s].push({
                    cp: bbn.cp.immunizeValue(tmp),
                    data: a.data || {}
                  });
                }
              }
              catch (e) {
                console.log([a, slot, e]);
                bbn.fn.error(bbn._("Impossible to read the slot %s in %s", slot, a.name));
              }
            });
          });
        });
      }
    },
    created(){
      appui.register('appui-email-webmail', this);
      this.updateTree();
    },
    mounted(){
      this.$set(appui.pollerObject, 'appui-email', {
        hashes: this.hash
      });
      appui.poll();

      if (this.source.accounts?.length) {
        bbn.fn.each(this.source.accounts, a => this.startAccountIdle(a.id));
      }
    },
    beforeDestroy(){
      appui.unregister('appui-email-webmail');
      if (this.syncId) {
        bbn.fn.abort(this.syncId);
        this.syncId = false;
      }

      bbn.fn.iterate(this.accountsIdle, (val, idAccount) => this.stopAccountIdle(idAccount));
    },
    watch: {
      currentFolder(){
        this.searchClear();
        this.$nextTick(() => {
          this.reloadMailList();
        })
      }
    },
    components: {
      treeItem: {
        template: `
          <div class="appui-email-webmail-tree-item bbn-grid bbn-vmiddle bbn-right-spadding"
               :style="currentStyle">
            <i :class="source.data.icon"/>
            <span :class="{'bbn-b bbn-secondary-text-alt': isAccount}"
                  bbn-html="source.data.text"/>
            <span bbn-if="!isAccount"
                  bbn-text="num"/>
            <i bbn-if="isAccount && !source.data.connected"
               class="nf nf-md-sync_off bbn-s"
               style="color: var(--error-text)"/>
          </div>`,
        props: ['source'],
        data(){
          return {
            isAccount: this.source.data.type === 'account'
          }
        },
        computed: {
          currentStyle(){
            return {
              'grid-template-columns': this.isAccount ?
                'max-content auto' + (this.source.data.connected ? '' : ' max-content') :
                '1.2rem auto max-content',
              'column-gap': 'var(--xsspace)'
            }
          },
          num(){
            return this.source.data.unseen || '';
          }
        }
      }
    }
  };
})()