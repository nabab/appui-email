<div class="bbn-overlay">
  <bbn-table ref="table"
              @ready="setSelected"
              :source="source.root + 'data/home'"
              :info="true"
              :pageable="true"
              uid="id"
              :sortable="true"
              :filterable="true"
              :showable="true"
              :multifilter="true"
              :editable="true"
              :popup="getPopup()"
              :toolbar="[{
                label: '<?= _('New mailing') ?>',
                icon: 'nf nf-fa-plus',
                action: insert,
              }, {
                label: '<?= _('Emails ready') ?>',
                icon: 'nf nf-fa-envelope_o',
                action: openEmailsTab,
              },{
                label: '<?= _('Emails sent') ?>',
                icon: 'nf nf-fa-envelope',
                action: openEmailsSentTab,
              },{
                label: '<?= _('Letters types') ?>',
                icon: 'nf nf-fa-list',
                action: openLettersTypesTab, 
              }]"
              editor="appui-email-form"
              :order="[{
                field: 'sent',
                dir: 'DESC'
              }]"
              :tr-class="r => r.priority < 5 ? 'bbn-bg-light-red' : ''">
    <bbns-column label="<?= _("ID") ?>"
                  field="id"
                  :filterable="false"
                  :editable="false"
                  :sortable="false"
                  :invisible="true"/>
    <bbns-column label="<?= _("content") ?>"
                  field="content"
                  :filterable="false"
                  :editable="false"
                  :sortable="false"
                  :invisible="true"/>
    <bbns-column label="<?= _("Status") ?>"
                  field="state"
                  :width="80"
                  :source="status"/>
    <bbns-column label="<?= _("Infos") ?>"
                  :render="renderOfficiel"
                  :filterable="false"
                  :editable="false"
                  :sortable="false"
                  cls="bbn-m"
                  :width="200"/>
    <bbns-column field="priority"
                  label="<?= _("Priority") ?>"
                  type="number"
                  :invisible="true"/>
    <bbns-column field="attachments"
                  :invisible="true"
                  :default="[]"/>
    <bbns-column label="<?= _("Date") ?>"
                  field="sent"
                  :width="140"
                  type="datetime"
                  :required="true"
                  :nullable="true"/>
    <bbns-column label="<?= _("Recipients") ?>"
                  field="recipients"
                  :width="160"
                  :source="source.recipients"
                  :render="renderRecipients"
                  :required="true"/>
    <bbns-column label="<?= _("Object") ?>"
                  field="title"
                  :min-width="200"
                  :required="true"/>
    <bbns-column label="<?= _("Sender") ?>"
                  field="sender"
                  :width="160"
                  :sortable="false"
                  :source="source.senders"
                  :default="source.senders[0].value"
                  :required="true"
                  :invisible="true"/>
    <bbns-column label="<?= _("Total emails") ?>"
                  field="total"
                  :width="60"
                  :invisible="true"
                  type="number"
                  :editable="false"
                  :filterable="false"
                  :sortable="false"/>
    <bbns-column flabel="<?= _("Emails succeeded") ?>"
                  field="success"
                  label="<i class='nf nf-fa-check bbn-green'></i>"
                  :invisible="true"
                  :width="60"
                  type="number"
                  :editable="false"
                  :filterable="false"
                  :sortable="false"/>
    <bbns-column flabel="<?= _("Emails failed") ?>"
                  field="failure"
                  label="<i class='nf nf-fa-times bbn-red'></i>"
                  :invisible="true"
                  :width="60"
                  type="number"
                  :filterable="false"
                  :editable="false"
                  :sortable="false"/>
    <bbns-column flabel="<?= _("Emails ready") ?>"
                  field="ready"
                  label="<i class='nf nf-fa-check bbn-blue'></i>"
                  :invisible="true"
                  :width="60"
                  type="number"
                  :editable="false"
                  :sortable="false"/>
    <bbns-column width="100"
                  label="<?= _("Action") ?>"
                  :component="$options.components.menu"
                  :source="sourceMenu"/>
    </bbns-column>
  </bbn-table>
</div>
