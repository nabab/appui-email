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
                          icon="nf nf-oct-sync"
                          :disabled="!!syncId"/>
            </div>
          </bbn-toolbar>
        </div>
        <div class="bbn-flex-fill bbn-bottom-xspadding bbn-right-xspadding">
          <bbn-tree :source="treeSource"
                    uid="id"
                    :menu="treeMenu"
                    :opened="true"
                    @select="selectFolder"
                    ref="tree"
                    :drag="true"
                    @move="onMove"
                    @dragstart="onMoveStart"
                    :selectable="item => item.data.type !== 'account'"
                    class="appui-email-webmail-tree"
                    :item-component="$options.components.treeItem"/>
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
                  <bbn-input :button-right="isSearching ? 'nf nf-fa-times' : 'nf nf-fa-search'"
                             bbn-model="currentSearch"
                             @keydown.enter.escape="onSearchKeydown"
                             @clickrightbutton="isSearching ? searchClear() : search()"
                             :disabled="!currentFolder"/>
                </div>
              </bbn-toolbar>
            </div>
            <bbn-kanban-element class="appui-email-webmail-list bbn-noradius bbn-flex-fill"
                                :source="root + 'webmail'"
                                component="appui-email-webmail-item"
                                :component-events="itemEvents"
                                :pageable="true"
                                :filterable="true"
                                :selection="true"
                                :multifilter="true"
                                :data="dataObj"
                                ref="mailList"
                                :sortable="true"
                                :showable="true"
                                :order="[{field: 'date', dir: 'DESC'}]"
                                :filters="currentSearchObj"/>
          </div>
        </bbn-pane>
        <bbn-pane :scrollable="!threads">
          <appui-email-webmail-reader bbn-if="selectedMail"
                                      :source="selectedMail"
                                      :thread="threads && !!selectedMail?.thread?.length"
                                      :slots="pluginsSlots?.reader"/>
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