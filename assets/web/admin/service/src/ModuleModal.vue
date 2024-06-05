<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div id="module-modal">
    <module-details-dialog/>
  </div>
</template>

<script>
import ModuleDetailsDialog from './components/dialogs/ModuleDetailsDialog';

export default {
  name: 'ModuleModal',
  components: {
    ModuleDetailsDialog,
  },
};
</script>
