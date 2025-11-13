<bbn-form :source="source"
          :action="root + 'webmail/actions/smtp'"
          :data="{
            action: source.id ? 'update' : 'insert'
          }"
          @success="onSuccess"
          @failure="onFailure">
  <div class="bbn-grid-fields bbn-padding">
    <div class="bbn-label"><?= _("Name") ?></div>
    <bbn-input bbn-model="source.name"
               :required="true"/>
    <div class="bbn-label"><?= _("Host") ?></div>
    <bbn-input bbn-model="source.host"
               type="hostname"
               :required="true"/>
    <div class="bbn-label"><?= _("Login") ?></div>
    <bbn-input bbn-model="source.login"
               :required="true"/>
    <div class="bbn-label"><?= _("Password") ?></div>
    <bbn-input bbn-model="source.pass"
               type="password"
               :required="true"/>
    <div class="bbn-label"><?= _("Encryption") ?></div>
    <bbn-dropdown :source="encryptions"
                  bbn-model="source.encryption"
                  :required="true"/>
    <div class="bbn-label"><?= _("Port") ?></div>
    <bbn-input bbn-model="source.port"
               type="number"
               :required="true"/>
    <div bbn-if="source.encryption !== 'none'"
         class="bbn-label"><?= _("Validate certificate") ?></div>
    <bbn-checkbox bbn-if="source.encryption !== 'none'"
                  bbn-model="source.validatecert"
                  :value="1"
                  :novalue="0"/>
  </div>
</bbn-form>