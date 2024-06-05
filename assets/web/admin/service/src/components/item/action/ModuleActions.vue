<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--
events:
    - enableEvent
    - removeEvent
    - upgradeRequestEvent
    - trySwitchEvent
    - installEvent
    - purchaseEvent
    - activateEvent
    - selectForUpgradeEvent
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <installed-module-actions
    v-if="type === 'installed'"
    :module="module"
    :is-tile="isTile"/>
  <upgrade-module-actions
    v-else-if="type === 'upgrade'"
    :module="module"
    :advanced-mode="upgradeAdvancedMode"
    :selected="selectedForUpgrade"
    @selectForUpgradeEvent="$emit('selectForUpgradeEvent', module, $event)"/>
</template>

<script>
import { mapState } from 'vuex';
import UpgradeModuleActions from './UpgradeModuleActions';

export default {
  name: 'ModuleActions',
  components: {
    UpgradeModuleActions,
  },
  props: {
    module: {
      type: Object,
      default: () => {},
    },
    type: {
      type: String,
      default: 'installed',
    },
    isTile: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    ...mapState({
      upgradeAdvancedMode: state => state.upgrades.advancedMode,
    }),
    selectedForUpgrade() {
      return this.$store.getters['upgrade/isSelected'](this.module.id);
    },
  },
};
</script>
