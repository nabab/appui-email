/**
 * Created by BBN Solutions.
 * User: Mirko Argentino
 * Date: 23/03/2018
 * Time: 15:47
 */
(() => {
  return {
    props: ['source'],
    computed:{
      num(){
        return bbn.fn.count(this.closest('bbn-container').getComponent().source.categories, {id_type: this.source.id_type});
      }
    },
    methods: {
      insert(){
        this.getPopup({
          width: 800,
          height: '90%',
          component: 'appui-email-types-form',
          source: {
            id_type: this.source.id_type,
            label: '',
            content: '',
            name: ''
          },
          label: bbn._("New letter type")
        });
      }
    }
  }
})();
