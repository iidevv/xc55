<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="modules-page page-wrapper">
    <installed-modules-list />
  </div>
</template>

<script>
import InstalledModulesList from '../list/InstalledModules';

export default {
  name: 'InstalledModulesPage',
  components: {
    InstalledModulesList,
  },
};
</script>
