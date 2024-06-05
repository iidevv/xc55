<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownAttribute, CheckEmptyScriptTag -->
<template>
  <modal-dialog
    :width="750"
    identifier="upgrade-changelog-dialog">

    <template
      v-slot:title="{ title }">
      <i18n
        :path="title"
        tag="h2"
        class="heading"/>
    </template>

    <template v-slot:default="{ params }">
      <div
        v-for="entry in params.entries"
        :key="entry.id">
        <h3>{{ entry.readableName }}</h3>
        <nl2br
          v-for="(changelog, index) in entry.docs.changelog"
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
  name: 'UpgradeChangelogDialog',
  components: {
    ModalDialog,
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-changelog-dialog {
  h3 {
    margin-top: 0;
  }

  &.scrollable {
    .dialog-buttons:before {
      box-shadow: 0 1px 30px 20px $white-color;
    }

    .buttons-content {
      padding: 0;
    }
  }
}
</style>
