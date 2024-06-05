<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownAttribute, CheckEmptyScriptTag -->
<template>
  <modal-dialog
    :width="750"
    identifier="module-changelog-dialog">
    <template
      v-slot:title="{ params }">
      <i18n
        v-if="params.entry"
        path="changelog-dialog.module.title"
        tag="h2"
        class="heading">
        {{ params.entry.readableName }}
      </i18n>
    </template>
    <template v-slot="{ params }">
      <div v-if="params.entry">
        <nl2br
          v-for="(changelog, index) in params.entry.docs.changelog"
          :key="index"
          :text="changelog"
          tag="p"/>
      </div>
    </template>
  </modal-dialog>
</template>

<script>
import ModalDialog from '../block/ModalDialog';

export default {
  name: 'ModuleChangelogDialog',
  components: {
    ModalDialog,
  },
};
</script>

<style lang="scss">
.module-changelog-dialog p {
  margin-top: 0;
}
</style>
