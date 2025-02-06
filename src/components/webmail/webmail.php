<!-- HTML Document -->
<div :class="[componentClass, 'bbn-overlay']">
  <bbn-splitter bbn-if="source.accounts.length"
                orientation="horizontal"
                :resizable="true"
                :collapsible="true">
    <bbn-pane :size="250">
      <div class="bbn-overlay bbn-flex-height">
        <div class="bbn-spadding">
          <bbn-toolbar class="bbn-no-border bbn-radius">
            <div class="bbn-vmiddle bbn-xspadding"
                 style="gap: var(--xsspace)">
              <bbn-button @click="createAccount"
                          :notext=true
                          :label="_('Create a new mail account')"
                          icon="nf nf-md-account_plus"/>
              <bbn-button @click="writeNewEmail"
                          :notext="true"
                          :label="_('Write new mail')"
                          icon="nf nf-fa-edit"/>
              <bbn-button @click="changeOrientation"
                          :notext="true"
                          :label="_('Change Webmail orientation to ' + (orientation == 'horizontal' ? 'vertical' : 'horizontal'))"
                          :icon="orientation == 'horizontal' ? 'nf nf-cod-split_vertical' : 'nf nf-cod-split_horizontal'"/>
              <bbn-button @click="onSyncClick"
                          :notext="true"
                          :label="_('Synchronize')"
                          icon="nf nf-oct-sync"/>
            </div>
          </bbn-toolbar>
        </div>
        <div class="bbn-flex-fill bbn-bottom-xspadding bbn-right-xspadding">
          <bbn-tree :source="treeData"
                    uid="id"
                    :menu="treeMenu"
                    :opened="true"
                    storage-full-name="appui-email-webmail-tree"
                    @select="selectFolder"
                    ref="tree"
                    :drag="true"
                    @move="onMove"/>
        </div>
        <div class="bbn-header bbn-spadding bbn-no-border bbn-radius bbn-smargin">
          <div bbn-if="syncId"
               class="bbn-middle">
            <bbn-loadicon class="bbn-right-sspace"/>
            <div class="bbn-ellipsis"
                bbn-html="syncMessage"/>
          </div>
          <div bbn-elseif="currentFolderObj"
               class="bbn-middle">
            {{currentFolderObj.num_msg}} <?= _("email(s)") ?>
          </div>
          <div bbn-else
               class="bbn-middle">
            {{source.accounts.length}} <?= _("account(s)") ?>
          </div>
        </div>
      </div>
    </bbn-pane>
    <bbn-pane>
      <bbn-splitter :orientation="orientation"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane size="50%">
          <div class="bbn-flex-height">
            <div class="bbn-spadding">
              <bbn-toolbar class="bbn-no-border bbn-radius">
                <!-- <div class="bbn-vmiddle bbn-xspadding"
                     style="gap: var(--xsspace)">
                </div> -->
              </bbn-toolbar>
            </div>
            <bbn-kanban-element bbn-if="orientation == 'horizontal'"
                                class="bbn-noradius bbn-flex-fill"
                                :source="source.root + 'webmail'"
                                component="appui-email-item"
                                :pageable="true"
                                :filterable="true"
                                :selection="true"
                                @select="tableSelect"
                                @unselect="tableUnselect"
                                :multifilter="true"
                                :data="dataObj"
                                ref="table"
                                :sortable="true"
                                :showable="true"
                                :order="[{field: 'date', dir: 'DESC'}]"/>
            <bbn-table bbn-else
                       :source="source.root + 'webmail'"
                       storage-full-name="appui-email-webmail-table"
                       :filterable="true"
                       :selection="true"
                       :expander="expanderComponent"
                       :expandable="hasExpander"
                       @focus="selectMessage"
                       @select="tableSelect"
                       @unselect="tableUnselect"
                       :multifilter="true"
                       :data="dataObj"
                       ref="table"
                       :sortable="true"
                       :showable="true"
                       :order="[{field: 'date', dir: 'DESC'}]"
                       :pageable="true"
                       class="bbn-flex-fill">
              <bbns-column label="<i class='nf nf-eye'></i>"
                           :flabel="_('Read')"
                           type="boolean"
                           :width="30"
                           field="read"/>
              <bbns-column label="<i class='nf nf-md-paperclip'></i>"
                           :flabel="_('Attachments')"
                           :width="30"
                           type="number"
                           field="attachments"
                           :render="showAttachments"/>
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
                           :render="showSubject"
                           field="subject"/>
              <bbns-column :label="_('Size')"
                           :width="100"
                           field="size"
                           :invisible="true"/>
            </bbn-table>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="true">
          <div class="bbn-overlay"
               bbn-if="selectedMail">
            <div class="bbn-overlay">
              <div class="bbn-flex-height">
                <div class="bbn-spadding">
                  <bbn-toolbar class="bbn-m bbn-no-border bbn-radius">
                    <div class="bbn-vmiddle bbn-xspadding"
                         style="gap: var(--xsspace)">
                      <bbn-button icon="nf nf-fa-mail_reply"
                                  :label="_('Reply')"
                                  :notext="true"
                                  @click="reply"/>
                      <bbn-button icon="nf nf-fa-mail_reply_all"
                                  :label="_('Reply All')"
                                  :notext="true"
                                  @click="replyAll"/>
                      <bbn-button icon="nf nf-fa-mail_forward"
                                  :label="_('Forward')"
                                  :notext="true"
                                  @click="forward"/>
                      <bbn-button icon="nf nf-md-tab_plus"
                                  :label="_('Open in a new tab')"
                                  :notext="true"
                                  @click="openTab"/>
                      <bbn-button icon="nf nf-md-window_restore"
                                  :label="_('Open in a new window')"
                                  :notext="true"
                                  @click="openWindow"/>
                      <bbn-button icon="nf nf-fa-archive"
                                  :label="_('Archive')"
                                  :notext="true"
                                  @click="archive"/>
                      <bbn-button icon="nf nf-weather-fire"
                                  :label="_('Set as junk')"
                                  :notext="true"
                                  @click="setAsJunk"/>
                      <bbn-button icon="nf nf-md-delete"
                                  :label="_('Delete')"
                                  :notext="true"
                                  @click="deleteMail"/>
                      <bbn-button icon="nf nf-fa-bug"
                                  :label="_('Transform in task')"
                                  :notext="true"
                                  @click="mailToTask"/>
                      <bbn-button icon="nf nf-md-folder_move"
                                  :label="_('Move')"
                                  :notext="true"
                                  @click="moveFolder"/>
                      <bbn-dropdown bbn-if="attachments.length"
                                    :source="attachments"
                                    source-text="name"
                                    source-value="name"
                                    bbn-model="selectedAttachment"/>
                      <bbn-dropdown bbn-if="attachments.length"
                                    :source="attachmentsMode"
                                    bbn-model="selectedMode"/>
                      <bbn-button bbn-if="attachments.length"
                                  @click="doMode"
                                  icon="nf nf-md-folder_download"
                                  :notext="true"/>
                    </div>
                  </bbn-toolbar>
                </div>
                <div bbn-if="selectedMail.id" class="bbn-flex bbn-spadding email-header">
                  <span class="bbn-medium bbn-bottom-xsmargin">{{ selectedMail.subject }}</span>
                  <span class="bbn-bottom-xsmargin">{{_('to: ')}}
                    <a bbn-if="extractedTo && extractedTo.name && extractedTo.email && extractedTo.email !== extractedTo.name" :href="'mailto:' + extractedTo.email" :label="extractedTo.email">{{extractedTo.name}}</a>
                    <a bbn-else-if="extractedTo" :href="'mailto:' + extractedTo.email" >{{extractedTo.email}}</a>
                    <span bbn-else>{{selectedMail.to}}</span>
                  </span>
                  <span class="bbn-bottom-xsmargin">{{_('from: ')}}
                    <a bbn-if="extractedFrom && extractedFrom.name && extractedFrom.email && extractedFrom.email !== extractedFrom.name" :href="'mailto:' + extractedFrom.email" :label="extractedFrom.email">{{extractedFrom.name}}</a>
                    <a bbn-else-if="extractedFrom" :href="'mailto:' + extractedFrom.email">{{extractedFrom.email}}</a>
                    <span bbn-else>{{selectedMail.from}}</span>
                  </span>
                  <span class="bbn-small">{{ formatDate(selectedMail.date) }}</span>
                </div>
                <hr style="margin:0">
                <div class="bbn-flex-fill">
                  <bbn-frame sandbox="allow-scripts"
                             :url="source.root + '/reader/' + selectedMail.id"
                             class="bbn-100"/>
                </div>
              </div>
            </div>
          </div>
          <div class="bbn-overlay bbn-middle"
               bbn-else>
            <div class="bbn-block bbn-large bbn-c"
                 bbn-text="_('Select an email to see its content here')"/>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-pane>
  </bbn-splitter>
  <div class="bbn-overlay bbn-middle"
       bbn-else>
    <div class="bbn-block bbn-lpadding bbn-lg">
      <p>
        <?= _("You have no account configured yet") ?>
      </p>
      <p>
        <bbn-button @click="createAccount"><?= _("Create a new mail account") ?></bbn-button>
      </p>
    </div>
  </div>
</div>