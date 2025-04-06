/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 20/03/2018
 * Time: 16:24
 */
(() => {
  return {
    mixins: [bbn.cp.mixins.basic, bbn.cp.mixins.localStorage],
    props: {
      storage: {
        type: Boolean,
        default: true
      },
      storageFullName: {
        type: String,
        default: 'appui-emails-page-types-warning'
      }
    },
    methods: {
      insert(){
        this.getPopup({
          width: 800,
          height: '90%',
          component: 'appui-email-types-form',
          source: {
            empty_categories: this.source.empty_categories,
            id_type: '',
            label: '',
            content: '',
            name: ''
          },
          label: bbn._("New letter type")
        });
      },
      toolbar() {
        return [{
          text: bbn._('Insert new '),
          action: () => {
            this.insert();
          },
          icon: 'nf nf-fa-plus'
        }]
      },
      renderButtons(row){
        return [{
          text: bbn._("Mod."),
          icon: "nf nf-fa-edit",
          notext: true,
          action: r => this.edit(r),
        }, {
          text: bbn._("Suppr."),
          icon: "nf nf-fa-trash",
          notext: true,
          action: r => this.removeItem(r),
          disabled: !!row.default
        }];
      },
      renderUser(row){
        return appui.getUserName(row.id_user)
      },
      edit(row){
        this.post(this.source.root + 'actions/types/get', {
          id_note: row.id_note,
          version: row.version
        }, d => {
          if (d.success && d.data) {
            d.data.hasVersions = d.data.version > 1;
            this.getPopup({
              width: 800,
              height: '90%',
              component: 'appui-email-types-form',
              source: d.data,
              label: bbn._("Edit letter type")
            })
          }
        })
      },
      removeItem(row){
        if ( row.id_note ){
          appui.confirm(bbn._("Are you sure you want to delete this letter?"), () => {
            this.post(this.source.root + 'actions/types/delete', {id_note: row.id_note}, (d) => {
              if ( d.success ){
								let idx = bbn.fn.search(this.source.categories, 'id_note', row.id_note);
								if ( idx > -1 ){
									this.source.categories.splice(idx, 1);
									this.$refs.table.updateData();
									appui.success(bbn._('Deleted'));
								}
              }
							else {
								appui.error(bbn._('Error'));
							}
            });
          });
        }
      },
      showWarning() {
        const doNotShowAgainValue = this.getStorage() || 0;
        const cp = this;
        this.getPopup({
          minWidth: 550,
          maxWidth: '80%',
          scrollable: false,
          closable: false,
          label: bbn._("Avertissement sur les lettres types"),
          component: {
            template: `
<div class="bbn-padding">
  <div class="bbn-w-100 bbn-c bbn-b">Attention!<br><br></div>
  <div class="bbn-w-100">
    Ici vous pouvez modifier les lettres types mais elles utilisent un système de "templates" avec lequel il vous faut être très précautionneux.<br><br>
    Le mieux est de dupliquer une lettre-type existante et de la modifier. Une fois terminée, mettez-là en défaut si elle est utilisée sur une fonctionnalité sans choix (ex: attestations), et allez la tester dans son contexte. Alors vous pourrez effacer l'ancienne ou bien la refaire passer en défaut si votre modification renvoie une erreur.
  </div>
  <div class="bbn-w-100 bbn-c">
    <br><br>
    <bbn-checkbox :label="doNotShowAgain"
                  bbn-model="doNotShowAgainValue"/>
    <br><br>
    <bbn-button @click="close"
                label="OK"/>
  </div>
</div>`,
            data() {
              return {
                doNotShowAgainValue,
                doNotShowAgain: bbn._("Ne plus demander à l'avenir")
              }
            },
            methods: {
              close() {
                if (this.doNotShowAgainValue) {
                  cp.setStorage(true);
                }

                this.closest('bbn-floater').close(true)
              }
            }
          }
        });
        
      }
    },
    created() {
      let types = this,
          mixins = [{
            data(){
              return {types: types}
            }
          }];
    },
    mounted() {
      if (this.getStorage()) {
        this.getContainer().addMenu({
          text: bbn._("Show warning message"),
          icon: "nf nf-cod-warning",
          action: () => {
            bbn.fn.log("JOJO")
            this.showWarning()
          }
        });
        this.getContainer().addMenu({
          text: bbn._("Show warning message when opening"),
          icon: "nf nf-fa-warning",
          action: () => {
            bbn.fn.log(bbn._("Show warning message at opening"))
            this.setStorage(false)
            this.showWarning()
          }
        })
      }
      else {
        this.$nextTick(() => {
          this.showWarning()
        });
      }
    }
	}
})();
