<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <module-link
    :module="entry"
    class="upgrade-preview-entry">
    <img
      :src="moduleIconUrl(entry)"
      :alt="moduleReadableName(entry)">
  </module-link>
</template>

<script>
import appConfig from '../../../config/app-config';
import ModuleLink from '../item/ModuleLink';

export default {
  name: 'UpgradePreviewEntry',
  components: {
    ModuleLink,
  },
  props: {
    entry: {
      type: Object,
      default: () => {},
    },
  },
  methods: {
    moduleIconUrl(entry) {
      const publicDir = appConfig.publicDir !== '' ? `/${appConfig.publicDir}` : '';

      return entry.id === 'CDev-Core'
        ? `${appConfig.url + publicDir}/assets/web/admin/images/core_image.png`
        : entry.iconURL;
    },
    moduleReadableName(entry) {
      return entry.id === 'CDev-Core' ? 'Core' : entry.readableName;
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-preview-entry {
  .router-link-active {
    cursor: pointer;
  }

  img {
    @include vr($width: 3, $height: 3);
    display: block;
  }
}
</style>
