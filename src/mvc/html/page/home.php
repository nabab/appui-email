<bbn-splitter orientation="horizontal"
              class="appui-email-mailings bbn-overlay">
  <bbn-pane :size="250" class="bbn-border-right">
		<bbn-splitter orientation="vertical">
			<bbn-pane class="bbn-large">
        <bbn-tree :source="menu"
                  :opened="true"
                  @select="setFilter"
                  ref="tree"
                  :path="treePath"
                  uid="id"
                  :style="{'pointer-events': disableTree ? 'none' : 'auto'}"
        ></bbn-tree>
      </bbn-pane>
			<bbn-pane size="50%">
				<div class="bbn-100 bbn-block info">
          <div class="bbn-header bbn-vmiddle title">
            <span><strong><?= _('LIVE INFO') ?></strong></span>
            <bbn-switch @change="toggleGetInfo" :checked="!!info.getInfo"></bbn-switch>
          </div>
					<div v-if="info.current.id"
							 class="bbn-block bbn-w-100"
					>
            <div class="bbn-header bbn-c"><?= _('IN PROGRESS') ?></div>
						<div class="bbn-spadding">
              <div><strong><?= _('Title') ?>:</strong> {{info.current.title}}</div>
              <div><strong><?= _('Recipients') ?>:</strong> {{info.current.recipients}}</div>
              <div><strong><?= _('Started') ?>:</strong> {{fixDate(info.current.moment)}}</div>
              <div><strong><?= _('Sent') ?>:</strong> {{info.current.sent}}</div>
            </div>
					</div>
					<div v-if="info.next.id"
							 class="bbn-block bbn-w-100"
					>
						<div class="bbn-header bbn-c"><?= _('NEXT') ?></div>
            <div class=" bbn-spadding">
              <div><strong><?= _('Title') ?>:</strong> {{info.next.title}}</div>
              <div><strong><?= _('Recipients') ?>:</strong> {{info.next.recipients}}</div>
              <div><strong><?= _('Start') ?>:</strong> {{fixDate(info.next.moment)}}</div>
            </div>
					</div>
				</div>
		  </bbn-pane>
		</bbn-splitter>
	</bbn-pane>
  <bbn-pane>
    <bbn-router :autoload="false" class="bbn-h-100" :single="true" ref="tableRouter">
      <bbn-container :pinned="true" :load="false" :url="tableURL">
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
      </bbn-container>
    </bbn-router>
  </bbn-pane>
</bbn-splitter>