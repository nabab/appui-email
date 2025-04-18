// Javascript Document

(() => {
  return {
    data() {
      return {
        sync: this.parse(this.source.sync),
        isMount: true,
        checkInterval: null,
        checkDelay: 5000,
      }
    },
    created() {
      appui.register('appui-email-sync', this);
    },
    mounted() {
      const ct = this.closest('bbn-container')
      if (ct.isVisible) {
				this.startCheck();
      }
      ct.$on('view', ct => {
        this.startCheck();
      })
      ct.$on('unview', ct => {
        this.stopCheck();
      })
      this.$set(appui.pollerObject, 'appui-email-sync', null);
      appui.poll();
    },
    beforeDestroy() {
      appui.unregister('appui-email-sync');
      this.isMount = false;
      if (this.checkInterval) {
        clearInterval(this.checkInterval);
        this.checkInterval = null
      }
    },
    methods: {
      startCheck() {
        if (this.checkInterval) {
          clearInterval(this.checkInterval);
          this.checkInterval = null
        }
        this.checkInterval = setInterval(this.isFocused, this.checkDelay)
      },
      stopCheck() {
        if (this.checkInterval) {
          clearInterval(this.checkInterval);
        }
      },
      isFocused() {
        if (!this.isMount) return;

        bbn.fn.post('/email/data/sync/folders', {}, (d) => {
          this.sync = this.parse(d.sync)
        })
      },
      receive(d) {
        if (d && d.sync) {
          let sync = d.sync;
          this.sync = this.parse(sync);
        }
      },
      parse(sync) {
        let result =  {
          "running": {},
          "finished": {},
          "not started": {}
        };
        bbn.fn.log("SYNC", sync);
        for(let account in sync) {
          bbn.fn.log("ACCOUNT", sync[account]);
          for (let folder in sync[account].folders) {
            let f = sync[account].folders[folder];
            bbn.fn.log("folder", f, f.msg);
            if (!f || (f && f.msg === undefined) || (f && f.db_msg === undefined)) {
              bbn.fn.log(f);
              continue;
            }

            if (f.db_msg >= f.msg) {
              if (!result.finished[sync[account].id]) {
                result.finished[sync[account].id] = {}
                result.finished[sync[account].id].folders = [];
                result.finished[sync[account].id].name = sync[account].name
              }
              result.finished[sync[account].id].folders.push({'folder_name': f.name, 'db_msg': f.db_msg, 'msg': f.msg});
            } else {
              if (f.db_msg === 0 && f.msg > 0) {
                if (!result['not started'][sync[account].id]) {
                  result['not started'][sync[account].id] = {}
                  result['not started'][sync[account].id].folders = [];
                  result['not started'][sync[account].id].name = sync[account].name
                }
                result['not started'][sync[account].id].folders.push({'folder_name': f.name, 'db_msg': f.db_msg, 'msg': f.msg});
              } else {
                if (!result.running[sync[account].id]) {
                  result.running[sync[account].id] = {}
                  result.running[sync[account].id].folders = [];
                  result.running[sync[account].id].name = sync[account].name
                }
                result.running[sync[account].id].folders.push({'folder_name': f.name, 'db_msg': f.db_msg, 'msg': f.msg});
              }
            }
          }
        }

        bbn.fn.log(result);
        return result;
      }
    }
  }
})();