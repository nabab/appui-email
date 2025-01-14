<bbn-table :source="source.categories"
           editable="popup"
           ref="table"
           :order="[{field: 'text', dir: 'ASC'}]"
           :groupable="true"
           :group-by="3"
           uid="id_note"
           :toolbar="source.empty_categories.length ? toolbar : []">
  <bbns-column label="<?= _("ID") ?>"
              field="id_note"
              :width="100"
              :hidden="true"/>
  <bbns-column label="<i class='nf nf-fa-check bbn-c bbn-xl'></i>"
              flabel="<?= _("Default") ?>"
              field="default"
              :width="50"
              component="appui-email-types-default"
              cls="bbn-c"/>
  <bbns-column label="<?= _("Version") ?>"
              field="version"
              type="number"
              :width="50"
              cls="bbn-c"/>
  <bbns-column label="<?= _("Type") ?>"
              field="type"
              component="appui-email-types-type"/>
  <bbns-column label="<?= _("Name") ?>"
               field="name"/>
  <bbns-column label="<?= _("Object") ?>"
              field="title"/>
  <bbns-column label="<?= _("User") ?>"
              field="id_user"
              :render="renderUser"/>
  <bbns-column label="<?= _("Last edit") ?>"
              field="creation"
              type="date"
              :width="120"
              cls="bbn-c"/>
  <bbns-column label="<?= _("Text") ?>"
              field="content"
              :hidden="true"/>
  <bbns-column field="id_type"
              :invisible="true"
              :editable="false"/>
  <bbns-column width='100'
              flabel="<?= _('Actions') ?>"
              :buttons="renderButtons"
              cls="bbn-c"/>
</bbn-table>

