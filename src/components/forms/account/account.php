<bbn-form :source="account"
          @success="success"
          :data="{action: 'save'}"
          :action="cp.source.root + 'actions/account'">
  <div bbn-if="tree.length"
       class="bbn-overlay">
    <div class="bbn-flex-height">
      <div class="bbn-w-100 bbn-hpadding bbn-top-padding bbn-bottom-space">
        <bbn-button @click="backToConfig"
                    class="bbn-bottom-space">
          <?= _("Back") ?>
        </bbn-button>
        <div class="bbn-m bbn-b bbn-c">
          <?= _("Choose the folders you want to keep synchronized") ?>
        </div>
      </div>
      <div class="bbn-padding bbn-flex-fill">
        <bbn-tree :source="tree"
                  ref="tree"
                  :selection="true"
                  uid="uid"
                  :opened="true"
                  :scrollable="false"/>
      </div>
    </div>
  </div>
  <div bbn-else
       class="bbn-w-100">
    <div class="bbn-grid-fields bbn-padding bbn-m">
      <div class="bbn-label"><?= _("Account type") ?></div>
      <bbn-dropdown :source="types"
                    source-value="id"
                    placeholder="<?= _("Choose a type of account") ?>"
                    bbn-model="account.type"
                    autocomplete="off"
                    :required="true"/>
      <div class="bbn-label"><?= _("eMail address") ?></div>
      <bbn-input bbn-model="account.email"
                type="email"
                autocomplete="off"
                :required="true"/>
      <div class="bbn-label"><?= _("Login") ?></div>
      <bbn-input bbn-model="account.login"
                autocomplete="off"
                :required="true"/>
      <div class="bbn-label"><?= _("Password") ?></div>
      <bbn-input bbn-model="account.pass"
                type="password"
                :no-save="true"
                :required="true"/>
      <div class="bbn-label"><?= _("Private account") ?></div>
      <bbn-switch bbn-model="account.locale"
                  :value="true"
                  :novalue="false"/>
      <template bbn-if="['imap', 'pop3'].includes(accountCode)">
        <div class="bbn-label"><?= _("Use SSL") ?></div>
        <bbn-checkbox :value="1"
                      :novalue="0"
                      bbn-model="account.ssl"/>
        <div class="bbn-label"><?= _("Incoming server") ?></div>
        <bbn-input type="hostname"
                   bbn-model="account.host"
                   autocomplete="off"
                   :required="true"/>
        <div bbn-if="account.host"
             class="bbn-grid-full bbn-c">
          <a href="javascript:;"
             @click="hasSMTP = !hasSMTP">
            <?= _("Click here to change the outgoing server configuration if it is different") ?>
          </a>
        </div>
        <div bbn-if="hasSMTP"
             class="bbn-label">
          <?= _("Outgoing server") ?>
        </div>
        <bbn-input bbn-if="hasSMTP"
                   bbn-model="account.smtp"
                   type="hostname"
                   autocomplete="off"
                   :required="true"/>
      </template>
      <div bbn-if="errorState"
           class="bbn-grid-full bbn-c bbn-b bbn-state-error bbn-padding">
        <?= _("Impossible to connect to the mail server") ?>
      </div>
    </div>
  </div>
</bbn-form>