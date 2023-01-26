<!-- HTML Document -->

<bbn-table :source="root + 'data/response'"
           storage-full-name="appui-email-webmail-widget-table-table"
           :filterable="true"
           :selection="true"
           :multifilter="true"
           :data="source"
           :sortable="true"
           :showable="true"
           :order="[{field: 'date', dir: 'DESC'}]"
           :pageable="true">
  <bbns-column title="<i class='nf nf-eye'></i>"
               :ftitle="_('Read')"
               type="boolean"
               :width="30"
               field="read"/>
  <bbns-column title="<i class='nf nf-mdi-paperclip'></i>"
               :ftitle="_('Attachments')"
               :width="30"
               type="number"
               field="attachments"/>
  <bbns-column :title="_('Date')"
               type="datetime"
               :width="120"
               field="date"/>
  <bbns-column :title="_('From')"
               editor="bbn-autocomplete"
               :width="200"
               :source="source.contacts"
               field="id_sender"/>
  <bbns-column :title="_('Subject')"
               field="subject"/>
  <bbns-column :title="_('Size')"
               :width="100"
               field="size"
               :hidden="true"/>

</bbn-table>