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
            <div class="bbn-flex-fill bbn-vmiddle bbn-xspadding"
                 style="gap: var(--xsspace); justify-content: space-between">
              <div>
                <bbn-button @click="createAccount"
                            :notext=true
                            :label="_('Create a new mail account')"
                            icon="nf nf-md-mailbox"/>
                <bbn-button @click="changeOrientation"
                            :notext="true"
                            :label="_('Change Webmail orientation to ' + (orientation == 'horizontal' ? 'vertical' : 'horizontal'))"
                            :icon="orientation == 'horizontal' ? 'nf nf-cod-split_vertical' : 'nf nf-cod-split_horizontal'"/>
              </div>
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
                    @select="selectFolder"
                    ref="tree"
                    :drag="true"
                    @move="onMove"
                    @dragstart="onMoveStart"
                    :selectable="item => item.data.type !== 'account'"/>
        </div>
        <div class="bbn-header bbn-spadding bbn-no-border bbn-radius bbn-smargin"
             style="min-height: 2.5rem">
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
                <div class="bbn-flex-fill bbn-vmiddle bbn-xspadding"
                     style="gap: var(--xsspace); justify-content: space-between">
                  <bbn-button @click="writeNewEmail"
                              :notext="true"
                              :label="_('Write new mail')"
                              icon="nf nf-fa-edit"/>
                  <bbn-input button-left="nf nf-fa-search"
                             :disabled="true"/>
                </div>
              </bbn-toolbar>
            </div>
            <bbn-kanban-element class="appui-email-webmail-list bbn-noradius bbn-flex-fill"
                                :source="root + 'webmail'"
                                component="appui-email-webmail-item"
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
            <!--<bbn-table bbn-else
                       :source="root + 'webmail'"
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
            </bbn-table>-->
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="!threads">
          <appui-email-webmail-reader bbn-if="selectedMail"
                                      :source="selectedMail"
                                      :thread="threads"/>
          <div bbn-else
               class="bbn-overlay bbn-middle">
            <div class="bbn-block bbn-spadding bbn-c"
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