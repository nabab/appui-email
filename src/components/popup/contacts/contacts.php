<div class="bbn-overlay">
  <bbn-table :source="root + 'webmail/contacts'"
             :pageable="true"
             :filterable="true"
             :sortable='true'
             :search="true"
             :search-fields="['name', 'email']"
             search-operator="contains"
             :toolbar="() => { return [] }"
             :limits="[]"
             uid="email"
             @toggle="onToggle"
             :selection="true">
    <bbns-column field="name"
                 label="<?=_("Name")?>"/>
    <bbns-column field="email"
                 label="<?=_("e-Mail")?>"/>
  </bbn-table>
</div>