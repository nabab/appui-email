<!-- HTML Document -->
<div :class="[
             componentClass,
             'bbn-overlay'
             ]">
  <bbn-splitter orientation="horizontal"
                v-if="source.accounts.length"
                :resizable="true"
                :collapsible="true">
    <bbn-pane :size="250">
      <div class="bbn-overlay bbn-flex-height">
        <div class="bbn-header">
          <bbn-button @click="createAccount"
                      class="bbn-left-xsspace"
                      :notext=true
                      :label="_('Create a new mail account')"
                      icon="nf nf-mdi-account_plus"></bbn-button>
          <bbn-button @click="writeNewEmail"
                      class="bbn-left-xsspace"
                      :notext="true"
                      :label="_('Write new mail')"
                      icon="nf nf-fa-edit"></bbn-button>
          <bbn-button @click="changeOrientation"
                      class="bbn-left-xsspace"
                      :notext="true"
                      :label="_('Change Webmail orientation to ' + (orientation == 'horizontal' ? 'vertical' : 'horizontal'))"
                      :icon="orientation == 'horizontal' ? 'nf nf-cod-split_vertical' : 'nf nf-cod-split_horizontal'"/>
        </div>
        <div class="bbn-flex-fill">
          <bbn-tree :source="treeData"
                    uid="id"
                    :menu="treeMenu"
                    :opened="true"
                    storage-full-name="appui-email-webmail-tree"
                    @select="selectFolder"
                    ref="tree"
                    :draggable="true"
                    @move="onMove"/>
        </div>
      </div>
    </bbn-pane>
    <bbn-pane>
      <bbn-splitter :orientation="orientation"
                    :resizable="true"
                    :collapsible="true">
        <bbn-pane size="50%">
          <bbn-column-list v-if="orientation == 'horizontal'"
                           class="bbn-border"
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
                           :order="[{field: 'date', dir: 'DESC'}]">
          </bbn-column-list>
          <bbn-table v-else
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
                     :pageable="true">
            <bbns-column label="<i class='nf nf-eye'></i>"
                         :flabel="_('Read')"
                         type="boolean"
                         :width="30"
                         field="read"/>
            <bbns-column label="<i class='nf nf-mdi-paperclip'></i>"
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
        </bbn-pane>
        <bbn-pane :scrollable="true">
          <div class="bbn-overlay"
               v-if="selectedMail">
            <div class="bbn-overlay">
              <div class="bbn-flex-height">
                <bbn-toolbar class="bbn-m"
                             style="padding-top: 5px">
                  <bbn-button icon="nf nf-fa-mail_reply"
                              class="bbn-left-xsspace"
                              :label="_('Reply')"
                              :notext="true"
                              @click="reply"/>
                  <bbn-button icon="nf nf-fa-mail_reply_all"
                              class="bbn-left-xsspace"
                              :label="_('Reply All')"
                              :notext="true"
                              @click="replyAll"/>
                  <bbn-button icon="nf nf-fa-mail_forward"
                              class="bbn-left-xsspace"
                              :label="_('Forward')"
                              :notext="true"
                              @click="forward"/>
                  <bbn-button icon="nf nf-mdi-tab_plus"
                              class="bbn-left-xsspace"
                              :label="_('Open in a new tab')"
                              :notext="true"
                              @click="openTab"/>
                  <bbn-button icon="nf nf-mdi-window_restore"
                              class="bbn-left-xsspace"
                              :label="_('Open in a new window')"
                              :notext="true"
                              @click="openWindow"/>
                  <bbn-button icon="nf nf-fa-archive"
                              class="bbn-left-xsspace"
                              :label="_('Archive')"
                              :notext="true"
                              @click="archive"/>
                  <bbn-button icon="nf nf-weather-fire"
                              class="bbn-left-xsspace"
                              :label="_('Set as junk')"
                              :notext="true"
                              @click="setAsJunk"/>
                  <bbn-button icon="nf nf-mdi-delete"
                              class="bbn-left-xsspace"
                              :label="_('Delete')"
                              :notext="true"
                              @click="deleteMail"/>
                  <bbn-button icon="nf nf-fa-bug"
                              class="bbn-left-xsspace"
                              :label="_('Transform in task')"
                              :notext="true"
                              @click="mailToTask"/>
                  <bbn-button icon="nf nf-mdi-folder_move"
                              class="bbn-left-xsspace"
                              :label="_('Move')"
                              :notext="true"
                              @click="moveFolder"/>
                  <bbn-dropdown v-if="attachments.length"
                                :source="attachments"
                                source-text="name"
                                source-value="name"
                                v-model="selectedAttachment"
                                class="bbn-left-xlspace"/>
                  <bbn-dropdown v-if="attachments.length"
                                :source="attachmentsMode"
                                v-model="selectedMode"
                                class="bbn-left-xsspace"/>
                  <bbn-button v-if="attachments.length"
                              class="bbn-left-xsspace"
                              @click="doMode"
                              icon="nf nf-mdi-folder_download"
                              :notext="true"/>

                </bbn-toolbar>
                <div v-if="selectedMail.id" class="bbn-flex bbn-spadding email-header">
                  <span class="bbn-medium bbn-bottom-xsmargin">{{ selectedMail.subject }}</span>
                  <span class="bbn-bottom-xsmargin">{{_('to: ')}}
                    <a v-if="extractedTo && extractedTo.name && extractedTo.email && extractedTo.email !== extractedTo.name" :href="'mailto:' + extractedTo.email" :label="extractedTo.email">{{extractedTo.name}}</a>
                    <a v-else-if="extractedTo" :href="'mailto:' + extractedTo.email" >{{extractedTo.email}}</a>
                    <span v-else>{{selectedMail.to}}</span>
                  </span>
                  <span class="bbn-bottom-xsmargin">{{_('from: ')}}
                    <a v-if="extractedFrom && extractedFrom.name && extractedFrom.email && extractedFrom.email !== extractedFrom.name" :href="'mailto:' + extractedFrom.email" :label="extractedFrom.email">{{extractedFrom.name}}</a>
                    <a v-else-if="extractedFrom" :href="'mailto:' + extractedFrom.email">{{extractedFrom.email}}</a>
                    <span v-else>{{selectedMail.from}}</span>
                  </span>
                  <span class="bbn-small">{{ formatDate(selectedMail.date) }}</span>
                </div>
                <hr style="margin:0">
                <div class="bbn-flex-fill">
                  <bbn-frame sandbox="allow-scripts" :src="source.root + 'reader/' + selectedMail.id" class="bbn-100"/>
                </div>
              </div>
            </div>
          </div>
          <div class="bbn-overlay bbn-middle"
               v-else>
            <div class="bbn-block bbn-large bbn-c"
                 v-text="_('Select an email to see its content here')"/>
          </div>
        </bbn-pane>
      </bbn-splitter>
    </bbn-pane>
  </bbn-splitter>
  <div class="bbn-overlay bbn-middle"
       v-else>
    <div class="bbn-block bbn-lpadding bbn-lg">
      <p>
        <?= _("You have no account configured yet") ?>
      </p>
      <p>
        <bbn-button @click="createAccount"><?= _("Create a new mail account") ?></bbn-button>
      </p>
    </div>
  </div>


  <script type="text/x-template" :id="scpName + '-editor'">
<bbn-form :source="account"
          @success="success"
          :data="{action: 'save'}"
          :action="cp.source.root + 'actions/account'">
  <div class="bbn-overlay" v-show="tree.length">
    <div class="bbn-flex-height">
      <div class="bbn-w-100">
        <div class="bbn-padding">
          <bbn-button @click="backToConfig"><?= _("Back") ?></bbn-button>
    </div>
        <div class="bbn-m bbn-b bbn-c">
          <?= _("Choose the folders you want to keep synchronized") ?>
    </div>
    </div>
      <div class="bbn-padding bbn-flex-fill">
        <bbn-tree :source="tree"
                  ref="tree"
                  :selection="true"
                  uid="uid"
                  :opened="true"/>
    </div>
    </div>
    </div>
  <div class="bbn-w-100" v-show="!tree.length">
    <div class="bbn-grid-fields bbn-padding bbn-m">
      <div class="bbn-label">
        <?= _("Account type") ?>
    </div>
      <bbn-dropdown :source="types"
                    source-value="id"
                    placeholder="<?= _("Choose a type of account") ?>"
                    v-model="account.type"
                    autocomplete="off"
                    :required="true"/>

      <div class="bbn-label">
        <?= _("Main eMail address for this account") ?>
    </div>
      <bbn-input v-model="account.email"
                 type="email"
                 autocomplete="off"
                 :required="true"/>

      <div class="bbn-label">
        <?= _("Login") ?>
    </div>
      <bbn-input v-model="account.login"
                 autocomplete="off"
                 :required="true"/>

      <div class="bbn-label">
        <?= _("Password") ?>
    </div>
      <bbn-input v-model="account.pass"
                 type="password"
                 :no-save="true"
                 :required="true"/>

      <div v-if="['imap', 'pop3'].includes(accountCode)"
           class="bbn-label">
        <?= _("Use SSL") ?>
    </div>
      <bbn-checkbox v-if="['imap', 'pop3'].includes(accountCode)"
                    :value="1"
                    :novalue="0"
                    v-model="account.ssl"/>

      <div v-if="['imap', 'pop3'].includes(accountCode)"
           class="bbn-label">
        <?= _("Incoming server") ?>
    </div>
      <bbn-input v-if="['imap', 'pop3'].includes(accountCode)"
                 type="hostname"
                 v-model="account.host"
                 autocomplete="off"
                 :required="true"/>

      <div class="bbn-grid-full bbn-c"
           v-if="account.host && ['imap', 'pop3'].includes(accountCode)">
        <a href="javascript:;" @click="hasSMTP = !hasSMTP">
          <?= _("Click here to change the outgoing server configuration if it is different") ?>
    </a>
    </div>

      <div v-if="hasSMTP && ['imap', 'pop3'].includes(accountCode)"
           class="bbn-label">
        <?= _("Outgoing server") ?>
    </div>
      <bbn-input v-if="hasSMTP && ['imap', 'pop3'].includes(accountCode)"
                 v-model="account.smtp"
                 type="hostname"
                 autocomplete="off"
                 :required="true"/>

      <div class="bbn-grid-full bbn-c bbn-b bbn-state-error bbn-padding"
           v-if="errorState">
        <?= _("Impossible to connect to the mail server") ?>
    </div>

    </div>
    </div>
    </bbn-form>
  </script>
</div>