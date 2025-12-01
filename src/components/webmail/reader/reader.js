(() => {
  return {
    mixins: [bbn.cp.mixins.basic],
    props: {
      source: {
        type: Object,
        required: true
      },
      overlay: {
        type: Boolean,
        default: false
      },
      thread: {
        type: Boolean,
        default: false
      },
      index: {
        type: Number,
        default: 0
      }
    },
    data(){
      const isInThread = this.$el.parentElement.classList.contains('bbn-kanban-element-item');
      return {
        root: appui.plugins['appui-email'] + '/',
        isFrameLoading: true,
        isInThread,
        mainReader: isInThread ? this.closest('appui-email-webmail-reader') : this,
        currentSelected: this.thread ? this.source.thread?.[0]?.id : this.source.id,
        webmail: appui.getRegistered('appui-email-webmail')
      }
    },
    computed: {
      isSelected(){
        return this.isInThread
         && (this.mainReader.source.thread?.length > 1)
         && (this.mainReader.currentSelected === this.source.id);
      },
      attachmentsSrc(){
        const src = [];
        if (this.source.attachments?.length) {
          bbn.fn.each(this.source.attachments, a => {
            src.push({
              text: a.name,
              icon: this.getFileIcon(a),
              items: [{
                text: bbn._('Download'),
                value: 'download',
                icon: 'nf nf-fa-download',
                action: () => this.download(a)
              }, {
                text: bbn._('Send to shared media'),
                icon: 'nf nf-md-image',
                action: () => this.sendToSharedMedia(a)
              }, {
                text: bbn._('Send to private media'),
                icon: 'nf nf-md-image_lock',
                action: () => this.sendToPrivateMedia(a)
              }]
            })
          });
          src.push({
            separator: true
          }, {
            text: bbn._('Download all'),
            icon: 'nf nf-fa-download',
            action: this.downloadAll
          }, {
            text: bbn._('Send all to shared media'),
            value: 'shared_media_all',
            icon: 'nf nf-md-image',
            action: this.sendAllToSharedMedia
          }, {
            text: bbn._('Send all to private media'),
            value: 'private_media_all',
            icon: 'nf nf-md-image_lock',
            action: this.sendAllToPrivateMedia
          });
        }

        return src;
      },
      recipients(){
        return this.source.to ? this.source.to.split(', ') : [];
      },
      recipientsNames(){
        return this.source.to_name ? this.source.to_name.split(', ') : [];
      },
      recipientsEmails(){
        return this.source.to_email ? this.source.to_email.split(', ') : [];
      },
      spamFolderId(){
        if (this.webmail?.currentAccountObj?.rules?.spam) {
          return bbn.fn.getField(
            this.webmail?.currentAccountObj?.folders || [],
            'id',
            {uid: this.webmail.currentAccountObj.rules.spam}
          );
        }

        return null;
      },
      archiveFolderId(){
        if (this.webmail?.currentAccountObj?.rules?.archive) {
          return bbn.fn.getField(
            this.webmail?.currentAccountObj?.folders || [],
            'id',
            {uid: this.webmail.currentAccountObj.rules.archive}
          );
        }

        return null;
      },
      otherFolders(){
        return bbn.fn.filter(this.webmail.folders || [], f => f.value !== this.webmail.currentFolder)
      }
    },
    methods: {
      onSelect(){
        this.mainReader.currentSelected = this.source.id;
      },
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
      reply(){
        if (this.mainReader.currentSelected) {
          bbn.fn.link(this.root + "webmail/write/reply/" + this.mainReader.currentSelected);
        }
      },
      replyAll(){
        if (this.mainReader.currentSelected) {
          bbn.fn.link(this.root + "webmail/write/reply_all/" + this.mainReader.currentSelected);
        }
      },
      forward(){
        if (this.mainReader.currentSelected) {
          bbn.fn.link(this.root + "webmail/write/forward/" + this.mainReader.currentSelected);
        }
      },
      archive(){
        if (this.archiveFolderId
          && this.mainReader.currentSelected
        ) {
          this.mainReader.confirm(bbn._('Do you want to move this email to archive?'), () => {
            this.post(this.root + 'webmail/actions/email/move', {
              id: this.mainReader.currentSelected,
              id_folder: this.archiveFolderId
            }, d => {
              if (d.success) {
                if ((this.mainReader.currentSelected === this.webmail.selectedMail?.id)
                  && (this.mainReader.currentSelected === this.source.id)
                ) {
                  this.webmail.selectedMail = null;
                }

                if ((this.webmail.currentFolder === this.source.id_folder)
                  || (this.spamFolderId === this.webmail.currentFolder)
                ) {
                  this.webmail.reloadTable();
                }

                appui.success(bbn._('Email moved to archive with success'))
              }
              else {
                appui.error(bbn._('An error occured when moving the email to archive'))
              }
            })
          })
        }
      },
      moveToSpam(){
        if (this.spamFolderId
          && this.mainReader.currentSelected
        ) {
          this.mainReader.confirm(bbn._('Do you want to mark this email as spam?'), () => {
            this.post(this.root + 'webmail/actions/email/move', {
              id: this.mainReader.currentSelected,
              id_folder: this.spamFolderId
            }, d => {
              if (d.success) {
                if ((this.mainReader.currentSelected === this.webmail.selectedMail?.id)
                  && (this.mainReader.currentSelected === this.source.id)
                ) {
                  this.webmail.selectedMail = null;
                }

                if ((this.webmail.currentFolder === this.source.id_folder)
                  || (this.spamFolderId === this.webmail.currentFolder)
                ) {
                  this.webmail.reloadTable();
                }

                appui.success(bbn._('Email marked as spam with success'))
              } else {
                appui.error(bbn._('An error occured when marking the email as spam'))
              }
            })
          })
        }
      },
      openTab(){
        if (this.mainReader.currentSelected) {
          bbn.fn.link(this.root + "webmail/view/" + this.mainReader.currentSelected);
        }
      },
      openWindow(){
        if (this.mainReader.currentSelected) {
          this.getPopup().load({
            url: this.root + "webmail/view/" + this.mainReader.currentSelected,
            width: '90%',
            height: '90%',
            maximizable: true
          })
        }
      },
      deleteMail(){
        if (this.mainReader.currentSelected) {
          this.confirm(bbn._('Do you want to delete this email?'), () => {
            this.post(this.root + 'actions/email/delete', {
              id: this.mainReader.currentSelected,
              status: "ready"
            }, d => {
              if (d.success) {
                appui.success(bbn._('Email deleted with success'))
              } else {
                appui.error(bbn._('An error occured when deleting the email'))
              }
            })
          })
        }
      },
      moveFolder() {
        if (this.otherFolders?.length) {
          this.getPopup({
            label: bbn._("Move email to another folder"),
            component: 'appui-email-webmail-email-move',
            componentOptions: {
              email: this.source.id,
              folders: this.otherFolders
            },
            componentEvents: {
              success: (d, selectedFolder) => {
                if (d.success) {
                  if ((this.mainReader.currentSelected === this.webmail.selectedMail?.id)
                    && (this.mainReader.currentSelected === this.source.id)
                  ) {
                    this.webmail.selectedMail = null;
                  }

                  if ((this.webmail.currentFolder === this.source.id_folder)
                    || (selectedFolder === this.webmail.currentFolder)
                  ) {
                    this.webmail.reloadTable();
                  }

                  appui.success(bbn._('Email moved successfully'));
                }
                else {
                  appui.error(d.error || bbn._('An error occurred while moving the email'));
                }
              },
              failure: d => {
                appui.error(d.error || bbn._('An error occurred while moving the email'));
              }
            }
          })
        }
      },
      mailToTask(){
        if (this.source.id) {

        }
      },
      onFrameLoaded(){
        const f = this.getRef('frame');
        f.contentWindow.document.addEventListener('click', e => {
          this.onSelect();
          if ((e.target?.tagName === 'A')
            && e.target?.attributes?.cid
          ) {
            this.download({name: e.target.getAttribute('cid')});
          }
        });

        if (f?.src) {
          setTimeout(() => {
            if (!this.overlay) {
              f.style.height = f.contentWindow.document.documentElement.scrollHeight + 'px';
            }

            this.isFrameLoading = false;
          }, 0)
        }
      },
      download(att){
        bbn.fn.download(this.root + 'webmail/actions/attachment/download', {
          id: this.source.id,
          filename: att.name
        });
      },
      downloadAll(){
        if (this.source.attachments?.length) {
          bbn.fn.each(this.source.attachments, att => {
            this.download(att);
          });
        }
      },
      saveAsMedia(mode, filename = null){
        if (this.source.attachments?.length) {
          this.post(this.root + "webmail/actions/attachment/media", {
            id: this.source.id,
            mode,
            filename
          }, d => {
            if (d.success) {
              appui.success()
            }
            else {
              appui.error()
            }
          });
        }
      },
      sendToSharedMedia(att) {
        if (this.source.attachments?.length) {
          this.confirm(bbn._('Do you want to send this file to shared media?'), () => {
            this.saveAsMedia('shared_media', att.name);
          });
        }
      },
      sendAllToSharedMedia() {
        if (this.source.attachments?.length) {
          this.confirm(bbn._('Do you want to send all files to shared media?'), () => {
            this.saveAsMedia('shared_media_all');
          });
        }
      },
      sendToPrivateMedia(att) {
        if (this.source.attachments?.length) {
          this.confirm(bbn._('Do you want to send this file to private media?'), () => {
            this.saveAsMedia('private_media', att.name);
          });
        }
      },
      sendAllToPrivateMedia() {
        if (this.source.attachments?.length) {
          this.confirm(bbn._('Do you want to send all files to private media?'), () => {
            this.saveAsMedia('private_media_all');
          });
        }
      },
      getFileIcon(attachment){
        switch (attachment.type) {
          case 'pdf':
            return 'nf nf-fa-file_pdf_o'
          case 'zip':
            return 'nf nf-fa-file_zip_o'
          case 'rar':
          case 'tar':
          case 'bz2':
          case 'gz':
          case '7z':
          case 'cab':
          case 'cab':
            return 'nf nf-fa-file_archive_o'
          case 'jpg':
          case 'jpeg':
          case 'png':
          case 'gif':
          case 'bmp':
          case 'svg':
            return 'nf nf-fa-file_image_o'
          case 'avi':
          case 'mov':
          case 'mkv':
          case 'mpg':
          case 'mpeg':
          case 'wmv':
          case 'mp4':
            return 'nf nf-fa-file_movie_o'
          case 'mp3':
          case 'wav':
            return 'nf nf-fa-file_sound_o'
          case 'php':
          case 'js':
          case 'html':
          case 'htm':
          case 'css':
          case 'less':
            return 'nf nf-fa-file_code_o'
          case 'txt':
          case 'rtf':
            return 'nf nf-fa-file_text_o'
          case 'doc':
          case 'docx':
          case 'odt':
            return 'nf nf-fa-file_word_o'
          case 'xls':
          case 'xlsx':
          case 'ods':
          case 'csv':
            return 'nf nf-fa-file_excel_o'
          case 'ppt':
          case 'pptx':
          case 'odp':
            return 'nf nf-fa-file_powerpoint_o'
          default:
            return 'nf nf-fa-file'
        }
      }
    }
  }
})();