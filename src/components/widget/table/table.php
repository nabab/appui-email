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
  <bbns-column label="<i class='nf nf-eye'></i>"
               :flabel="_('Read')"
               type="boolean"
               :width="30"
               field="read"/>
  <bbns-column label="<i class='nf nf-md-paperclip'></i>"
               :flabel="_('Attachments')"
               :width="30"
               type="number"
               field="attachments"/>
  <bbns-column :label="_('Date')"
               type="datetime"
               :width="120"
               field="date"/>
  <bbns-column :label="_('From')"
               editor="bbn-autocomplete"
               :width="200"
               :source="source.contacts"
               field="id_sender"/>
  <bbns-column :label="_('Subject')"
               field="subject"/>
  <bbns-column :label="_('Size')"
               :width="100"
               field="size"
               :invisible="true"/>

</bbn-table>