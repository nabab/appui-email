<bbn-table :source="root + tableSource"
           :pageable="true"
           :editable="false"
           :filterable="filterable"
           :selection="true"
           :toolbar="$options.components['toolbar']"
           :filters="filters"
           :data="tableData"
           ref="table"
>
  <bbns-column field="id"
               label="<?= _('ID') ?>"
               :invisible=true
  ></bbns-column>
  <bbns-column field="email"
               label="<?= _('e-Mail address') ?>"
               type="email"
  ></bbns-column>
  <bbns-column field="subject"
               label="<?= _('Title') ?>"
               :render="renderTitre"
               v-if="context !== 'details'"
  ></bbns-column>
  <bbns-column field="id_mailing"
               label="<?= _('e-Mailing') ?>"
               :render="renderMailing"
               :width="100"
               cls="bbn-c"
               v-if="context !== 'details'"
               :filterable="false"
  ></bbns-column>
  <bbns-column field="status"
               label="<?= _('Status') ?>"
               :source="status"
               :render="renderEtat"
               cls="bbn-c"
               :width="80"
               :filterable="false"
  ></bbns-column>
  <bbns-column field="delivery"
               label="<?= _('Date') ?>"
               cls="bbn-c"
               type="date"
               :width="120"
  ></bbns-column>
  <bbns-column field="read"
               label="<?= _('Read') ?>"
               :width="80"
               :invisible="true"
  ></bbns-column>
  <bbns-column :render="renderFiles"
               field="attachments" 
               cls="bbn-c"
               :width="50"
               label="<i class='nf nf-fa-paperclip bbn-xl'></i>"
               flabel="<?= _("Number of attached files") ?>"
               :sortable="false"
               :filterable="false"
  ></bbns-column>
  <bbns-column field="priority"
               flabel="<?= _('Priority') ?>"
               label="<i class='nf nf-md-truck_fast'></i>"
               :width="30"
               cls="bbn-c"
               :render="renderPriority"
               :filterable="false"
  ></bbns-column>
  <bbns-column width="120"
               :cls="{
                 'bbn-buttons-flex': (context !== 'sent'),
                 'bbn-c' : true
               }"
              :buttons="renderButtons"
  >
  </bbns-column>
</bbn-table>