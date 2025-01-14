<!--bbn-table :source="root + 'data/sent'"
           :pageable="true"
           :editable="false"
           :filterable="true"
           :filters="{
             logic: 'AND',
             conditions: [{
               field: 'status',
               operator: 'eq',
               value: 'success'
             }]
           }"
>
  <bbns-column field="id"
               label="<?= _('ID') ?>"
               :hidden=true
  ></bbns-column>
  <bbns-column field="email"
               label="<?= _('e-Mail address') ?>"
               type="email"
  ></bbns-column>
  <bbns-column field="subject"
               label="<?= _('Title') ?>"
               :render="renderTitre"
  ></bbns-column>
  <bbns-column field="id_mailing"
               label="<?= _('e-Mailing') ?>"
               :render="renderMailing"
               :width="100"
               cls="bbn-c"
  ></bbns-column>
  <bbns-column field="status"
               label="<?= _('Status') ?>"
               :source="status"
               :render="renderEtat"
               cls="bbn-c"
               :width="80"
  ></bbns-column>
  <bbns-column field="delivery"
               label="<?= _('Date') ?>"
               cls="bbn-c"
               type="datetime"
               :width="120"
  ></bbns-column>
  <bbns-column field="read"
               label="<?= _('Read') ?>"
               :width="80"
               :hidden="true"
  ></bbns-column>
</bbn-table-->
<appui-email-table :source="source" 
                    tableSource="data/sent"
                    :filters="{
                      logic: 'AND',
                      conditions: [{
                        field: 'status',
                        operator: 'eq',
                        value: 'success'
                      }]
                    }"
                    context="sent"
></appui-email-table>