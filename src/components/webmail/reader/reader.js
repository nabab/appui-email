(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        required: true
      }
    },
    data(){
      return {
        root: appui.plugins['appui-email'] + '/',
        isFrameLoading: true,
        attachmentSrc: [{
          text: bbn._('Download'),
          value: 'download',
          icon: 'nf nf-fa-download'
        }, {
          text: bbn._('Send to shared media'),
          value: 'shared_media',
          icon: 'nf nf-md-image'
        }, {
          text: bbn._('Send to private media'),
          value: 'private_media',
          icon: 'nf nf-md-image_lock'
        }],
        attachmentsSrc: [{
          text: bbn._('Download all'),
          value: 'download_all',
          icon: 'nf nf-fa-download'
        }, {
          text: bbn._('Send all to shared media'),
          value: 'shared_media_all',
          icon: 'nf nf-md-image'
        }, {
          text: bbn._('Send all to private media'),
          value: 'private_media_all',
          icon: 'nf nf-md-image_lock'
        }],
      }
    },
    methods: {
      formatDate(date) {
        const emailDate = dayjs(date);
        const currentDate = dayjs();
        if (emailDate.year() !== currentDate.year()) {
          return dayjs(date).format("lll");
        }
        else if (emailDate.format('DDMMYYYY') === currentDate.format('DDMMYYYY')) {
          return dayjs(date).format("LT");
        }
        else {
          return dayjs(date).format("lll");
        }
      },
      reply(){
        if (this.source.id) {
          bbn.fn.link(this.root + "webmail/write/reply/" + this.source.id);
        }
      },
      replyAll(){
        if (this.source.id) {
          bbn.fn.link(this.root + "webmail/write/reply_all/" + this.source.id);
        }
      },
      forward(){
        if (this.source.id) {
          bbn.fn.link(this.root + "webmail/write/forward/" + this.source.id);
        }
      },
      archive(){
        if (this.source.id) {

        }
      },
      setAsJunk(){
        if (this.source.id) {

        }
      },
      openTab(){
        if (this.source.id) {
          bbn.fn.link(this.root + "webmail/view/" + this.source.id);
        }
      },
      openWindow(){
        if (this.source.id) {

        }
      },
      deleteMail(){
        this.confirm(bbn._('Do you want to delete this email?'), () => {
          this.post(this.root + 'actions/email/delete', {
            id: this.sourcel.id,
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
              id : this.source.id,
              folders: this.folders,
              foldersData: this.foldersData,
            }
          }
        })
      },
      mailToTask(){
        if (this.source.id) {

        }
      },
      onFrameLoaded(a, b){
        const f = this.getRef('frame');
        if (f?.src) {
          this.isFrameLoading = false;
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
      showAttachments(row){
        if (row.attachments) {
          let attachments = bbn.fn.isString(row.attachments) ? JSON.parse(row.attachments) : row.attachments;
          return attachments.length
        }
        return '-';
      },
    }
  }
})();