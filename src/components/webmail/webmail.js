// Javascript Document
(() => {
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
        treeData: [],
        folders: [],
        foldersData: [],
        selectedMails: [],
        alreadySendUpdateError: false,
        moveTo: "",
        root: appui.plugins['appui-email'],
        newCount: 0,
        hash: this.source.hash,
        sync: null,
        attachments: [],
        selectedAttachment: "Attachments",
        extractedFrom: null,
        extractedTo: null,
        attachmentsMode: [
          {
            text: bbn._('Download'),
            value: 'download'
          },
          {
            text: bbn._('Download all'),
            value: 'download_all'
          },
          {
            text: bbn._('Send to shared media'),
            value: 'shared_media'
          },
          {
            text: bbn._('Send all to shared media'),
            value: 'shared_media_all'
          },
          {
            text: bbn._('Send to private media'),
            value: 'private_media'
          },
          {
            text: bbn._('Send all to private media'),
            value: 'private_media_all'
          }
        ],
        selectedMode: "download",
        syncId: false,
        syncMessage: ''
      };
    },
    computed: {
      dataObj(){
        if (!this.currentFolder) {
          return {
            id_folder: this.source.folder_types[1].id
          };
        }
        return {
          id_folder: this.currentFolder
        }
      },
      currentFolderObj(){
        if (this.currentAccount && this.currentFolder) {
          const account = bbn.fn.getRow(this.source.accounts, {id: this.currentAccount});
          if (account) {
            return bbn.fn.getRow(account.folders, {id: this.currentFolder}) || false;
          }
        }

        return false;
      }
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
      download() {
        bbn.fn.download(appui.plugins['appui-email'] + "/data/attachment/index/download/" + this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + this.selectedAttachment);
      },
      downloadAll() {
        for (const att of this.attachments) {
          if (att.name === 'Attachments')
            continue;
          bbn.fn.download(appui.plugins['appui-email'] + "/data/attachment/index/download/" + this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + att.name);
        }
      },
      sendToSharedMedia() {
        bbn.fn.post(appui.plugins['appui-email'] + "/data/attachment/index", {
          mode: 'shared_media',
          path: this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + this.selectedAttachment,
          id: this.selectedMail.id,
          filename: this.selectedAttachment
        }, (d) => {
          if (d.success) {
            appui.success(bbn._('Files was send to shared media'))
          } else {
            appui.error(bbn._('An error occured'))
          }
        })
      },
      sendAllToSharedMedia() {
        let success = true;
        for (const att of this.attachments) {
          bbn.fn.post(appui.plugins['appui-email'] + "/data/attachment/index", {
            mode: 'shared_media',
            path: this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + att.name,
            id: this.selectedMail.id,
            filename: att.name
          }, (d) => {
            if (!d.success) {
              success = false;
            }
          });
        }
        if (success) {
          appui.success(bbn._('All files was send to shared media'))
        } else {
          appui.error(bbn._('An error occured'))
        }
      },
      sendToPrivateMedia() {
        bbn.fn.post(appui.plugins['appui-email'] + "/data/attachment/index", {
          mode: 'private_media',
          path: this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + this.selectedAttachment,
          id: this.selectedMail.id,
          filename: this.selectedAttachment
        }, (d) => {
          if (d.success) {
            appui.success(bbn._('Files was send to shared media'))
          } else {
            appui.error(bbn._('An error occured'))
          }
        })
      },
      sendAllToPrivateMedia() {
        let success = true;
        for (const att of this.attachments) {
          bbn.fn.post(appui.plugins['appui-email'] + "/data/attachment/index", {
            mode: 'private_media',
            path: this.foldersData.find(folder => folder.id === this.selectedMail.id_folder).id_account + '/' + this.selectedMail.id + '/' + att.name,
            id: this.selectedMail.id,
            filename: att.name
          }, (d) => {
            if (!d.success) {
              success = false;
            }
          });
        }
        if (success) {
          appui.success(bbn._('All files was send to shared media'))
        } else {
          appui.error(bbn._('An error occured'))
        }
      },
      doMode() {
        switch (this.selectedMode) {
          case 'download':
            this.download();
            break;
          case 'download_all':
            this.downloadAll();
            break;
          case 'shared_media':
            this.sendToSharedMedia();
            break;
          case 'shared_media_all':
            this.sendAllToSharedMedia()
            break;
          case 'private_media':
            this.sendToPrivateMedia();
            break;
          case 'private_media_all':
            this.sendAllToPrivateMedia();
            break;
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
                bbn.fn.post(this.root + '/actions/account', {
                  action: 'get',
                  id: accountId
                } , d => {
                  if (d.account) {
                    this.source.accounts.push(d.account);
                    this.setTreeData();
                    tree.updateData().then(() => {
                      tree.reload()
                    })
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
                  this.getRef('table')?.updateData();
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
                bbn.fn.post(this.root + '/actions/account', {
                  action: 'get',
                  id: key
                } , d => {
                  if (d.account) {
                    let idx = bbn.fn.search(this.source.accounts, {id: d.account.id});
                    if (idx >= 0) {
                      this.source.accounts[idx] = d.account;
                      this.setTreeData();
                      tree.updateData().then(() => {
                        tree.reload()
                      })
                    }
                  }
                });
              }
            }
          }
          this.setTreeData();
          tree.updateData().then(() => {
            tree.reload()
          })
          this.hash = d;
          this.$set(appui.pollerObject, 'appui-email', {
            hashes: this.hash
          });
          appui.poll();
        }
      },
      tableSelect(col) {
        this.selectedMails.push(col.id);
      },
      tableUnselect(col) {
        this.selectedMails.splice(col.id);
      },
      onMove(source, dest, event) {
        if (dest.data.type !== "account" && source.data.id_account !== dest.data.id_account) {
          event.preventDefault();
          return false;
        }
        if (dest.data.type === "account" && source.data.id_account !== dest.data.uid) {
          event.preventDefault();
          return false;
        }
        if (source.data.type !== "folders") {
          event.preventDefault();
          return false;
        }
        bbn.fn.post(this.root + '/actions/folder/move', {
          to: dest.data,
          id_account: source.data.id_account,
          folders: this.getAllFolderChild(source.data)
        }, d => {
          let tree = this.getRef('tree');
          let idx = bbn.fn.search(this.source.accounts, { id: source.data.id})
          if (d.success) {
            appui.success(bbn._(`${source.data.text} folder and subfolders ar successfuly moved to ${dest.data.text}`));
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
        event.preventDefault();
        return true;
      },
      treeMenu(node) {
        res = []
        if (node.data.type === "account") {
          res.push({
            text: bbn._('Delete account'),
            icon: "nf nf-md-delete",
            action: () => {
              this.deleteAccount(node.data.uid)
              appui.poll();
            }
          })
        }
        if (node.data.type !== "folder_types") {
          res.push({
            text: bbn._('Create folder'),
            icon: "nf nf-fa-plus",
            action: () => {
              this.getPopup({
                label: bbn._("Folder name"),
                component: "appui-email-forms-create",
                source: {
                  id_account: node.data.type === "account" ? node.data.uid : node.data.id_account,
                  id_parent: node.data.id || null,
                }
              })
              appui.poll();
            }
          })
        }
        if (node.data.type !== "account" && node.data.type !== "folder_types") {
          res.push({
            text: bbn._('Remove folder'),
            icon: "nf nf-md-folder_remove",
            action: () => {
              this.removeFolder(this.getAllFolderChild(node.data), node.data.text, node.data.id_account);
              appui.poll();
            }
          },
                   {
            text: bbn._('Rename Folder'),
            icon: "nf nf-md-rename_box",
            action: () => {
              this.getPopup({
                label: bbn._("Folder new name"),
                component: "appui-email-forms-rename",
                source: {
                  name: node.data.text,
                  id_account: node.data.type === "account" ? node.data.uid : node.data.id_account,
                  folders: this.getAllFolderChild(node.data)
                }
              })
              appui.poll();
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
        let icon = {
          Trash: "nf nf-fa-trash",
          INBOX: "nf nf-fa-inbox",
          spam: "nf nf-md-fire",
          Sent: "nf nf-md-inbox_arrow_up",
          Drafts: "nf nf-md-file_document"
        }
        let r = [];
        let fn = (ar, id_account) => {
          let res = [];
          bbn.fn.each(this.source.folder_types, ft => {
            bbn.fn.each(ar, a => {
              a.type = "folder";
              a.icon = icon[a.text] || "";
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
            let folders = bbn.fn.clone(a.folders.filter(el => el.subscribed !== false));
            r.push({
              text: a.login,
              id: a.id,
              uid: a.id,
              items: fn(folders, a.id),
              type: "account"
            });
          });
        }
        this.treeData = r;
      },
      getFolders() {
        if (this.selectedMail) {
          this.post(this.root + '/webmail/get_folders', {
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
      showAttachments(row){
        if (row.attachments) {
          let attachments = bbn.fn.isString(row.attachments) ? JSON.parse(row.attachments) : row.attachments;
          return attachments.length
        }
        return '-';
      },
      selectFolder(node) {
        this.currentAccount = node.data.id_account;
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
        this.attachments = this.selectedMail.attachments ? JSON.parse(this.selectedMail.attachments) : [];
        if (this.attachments.length) {
          if (this.attachments.length === 1) {
            this.selectedAttachment = this.attachments[0].name;
          } else {
            this.attachments.unshift({name: 'Attachments'});
            this.selectedAttachment = "Attachments";
          }
        }
        this.getFolders();
        this.extractedFrom = this.extractNameAndEmail(this.selectedMail.from);
        this.extractedTo = this.extractNameAndEmail(this.selectedMail.to);
      },
      selectedMessageIDisSame(id) {
        if (this.selectedMail === null) {
          return false;
        }
        return this.selectedMail.id === id;
      },
      createAccount() {
        this.getPopup({
          height: 450,
          label: bbn._("eMail account configuration"),
          component: 'appui-email-forms-account',
        })
        appui.poll();
      },
      treeMapper(a) {
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
        this.newCount++;
        bbn.fn.link(this.source.root + "webmail/write/new/" + this.newCount.toString());
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
          this.post(this.root + '/actions/email/delete', {
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
          label: bbn._("Folder changer"),
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
      },
      synchronize(idAccount, idFolder){
        const url = this.root + '/actions/email/sync';
        const data = {
          id_account: bbn.fn.isString(idAccount) ? idAccount : false,
          id_folder: bbn.fn.isString(idFolder) ? idFolder : false
        };
        this.syncId = bbn.fn.getRequestId(url, data);
        this.syncMessage = '<span>' + bbn._('Synchronizing %d/%d', 0, this.currentFolderObj.num_msg - this.currentFolderObj.db_num_msg) + '</span>';
        bbn.fn.stream(
          url,
          d => {
            bbn.fn.log('stream', d);
            if (!d.success && d.data?.error) {
              bbn.fn.warning(d.data.error);
              appui.error(bbn._('Failed synchronization'));
              this.syncMessage = '<span class="bbn-red">' + bbn._('Failed synchronization') + '</span>';
              setTimeout(() => {
                this.syncId = false;
              }, 3000);
            }
            else if (d.isSynchronizing) {
              this.syncMessage = '<span>' + bbn._('Synchronizing %d/%d', d.synchronized, this.currentFolderObj.num_msg - this.currentFolderObj.db_num_msg) + '</span>';
            }
            else if (d.success) {
              this.syncMessage = '<span class="bbn-green">' + bbn._('Synchronization successful') + '</span>';
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
      },
      abortSync(){
        if (this.syncId) {
          bbn.fn.abort(this.syncId);
          this.syncId = false;
        }
      }
    },
    watch: {
      currentFolder(){
        this.$forceUpdate();
        this.$nextTick(() => {
          this.getRef('table').updateData()
        })
      }
    },
    created(){
      this.setTreeData();
      appui.register('appui-email', this);
    },
    mounted(){
      this.$set(appui.pollerObject, 'appui-email', {
        hashes: this.hash
      });
      appui.poll();
    },
    beforeDestroy(){
      if (this.syncId) {
        bbn.fn.abort(this.syncId);
        this.syncId = false;
      }
    }
  };
})()