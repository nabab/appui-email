<div class="bbn-overlay">
  <bbn-table :source="root + 'webmail/contacts'"
             :pageable="true"
             :filterable="true"
             :sortable='true'
             @click-row="rowClicked"
             :search="true"
             :search-fields="['name', 'email']"
             search-operator="contains"
             :toolbar="() => { return [] }"
             :selection="true">
    <bbns-column field="name"></bbns-column>
    <bbns-column field="email"></bbns-column>
  </bbn-table>
</div>