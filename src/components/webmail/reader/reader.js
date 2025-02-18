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
        attachmentsSrc: [{
          text: bbn._('Download all'),
          value: 'download_all',
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
      onFrameLoaded(){
        const f = this.getRef('frame');
        if (f?.src) {
          this.isFrameLoading = false;
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