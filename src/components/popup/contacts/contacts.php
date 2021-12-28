<div class="bbn-overlay">
  <bbn-table :source="root + 'webmail/contacts'"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :sortable='true'
             @click-row="rowClicked">
    <bbns-column field="name"></bbns-column>
    <bbns-column field="email"></bbns-column>
  </bbn-table>
</div>