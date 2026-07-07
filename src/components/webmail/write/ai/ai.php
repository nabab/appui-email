<div class="appui-email-webmail-write-ai bbn-overlay">
  <bbn-form class="bbn-overlay"
            @success="success"
            @cancel="cancel"
            :buttons="formButtons"
            :source="formSource"
            mode="big"
            :scrollable="false"
            submit-text="<?= _("Replace") ?>"
            ref="form">
    <div bbn-if="isLoading"
         class="bbn-overlay bbn-flex-column bbn-middle bbn-padding"
         style="row-gap: 1.5rem">
      <div class="appui-email-webmail-write-ai-icons">
        <i class="nf nf-md-robot_outline"/>
        <i class="nf nf-md-robot_excited_outline"/>
        <i class="nf nf-md-robot_dead_outline"/>
        <i class="nf nf-md-robot_happy_outline"/>
      </div>
      <bbn-loadicon class="bbn-right-sspace bbn-vmiddle"
                    :size="32"/>
      <div class="bbn-lg bbn-b"><?= _('AI is processing your request...') ?></div>
    </div>
    <bbn-rte bbn-else
             bbn-model="formSource.content"
             :autosize="false"
             height="100%"
             class="bbn-overlay"/>
  </bbn-form>
</div>