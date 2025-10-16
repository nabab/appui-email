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
      }
    },
    methods: {
      onSelect(){
        this.mainReader.currentSelected = this.source.id;
      },
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
        if (this.mainReader.currentSelected) {

        }
      },
      setAsJunk(){
        if (this.mainReader.currentSelected) {

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
      },
      getAttachmentSrc(att){
        return [{
          text: bbn._('Download'),
          value: 'download',
          icon: 'nf nf-fa-download',
          action: () => this.download(att)
        }, {
          text: bbn._('Send to shared media'),
          value: 'shared_media',
          icon: 'nf nf-md-image',
          action: () => this.sendToSharedMedia(att)
        }, {
          text: bbn._('Send to private media'),
          value: 'private_media',
          icon: 'nf nf-md-image_lock',
          action: () => this.sendToPrivateMedia(att)
        }]
      }
    }
  }
})();