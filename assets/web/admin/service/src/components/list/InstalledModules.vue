<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="installed-modules module-section">
    <modules-page
      :filter="defaultFilter"
      :show-display-mode-switcher="true"/>
  </div>
</template>

<script>
import ModulesPage from './ModulesPage';

export default {
  name: 'InstalledModules',
  components: { ModulesPage },
  data() {
    return {
      defaultFilter: { sortBy: '', installed: true },
    };
  },
};
</script>
