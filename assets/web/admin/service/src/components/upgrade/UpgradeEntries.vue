<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div class="module-section grid-list">
    <modules-list
      v-if="totalCount > 0"
      :modules="modules"
      :show-on-sale="false"
      type="upgrade-module"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import ModulesList from '../list/ModulesList';

export default {
  name: 'UpgradeEntries',
  components: {
    ModulesList,
  },
  props: {
    type: {
      type: String,
      default: '',
    },
  },
  computed: {
    ...mapState({
      totalCount(state) {
        return state.upgrades.count;
      },
      fetchedEntries(state) {
        return state.upgrades.upgrade;
      },
    }),
    modules() {
      const entries = this.type && this.fetchedEntries[this.type]
        ? this.fetchedEntries[this.type]
        : [];

      return entries.map((item) => {
        const module = item;

        if (this.isLicenseExpired(module.id)) {
          module.messages = [{
            type: 'warning',
            message: 'module_state_message.upgrade.license-warning',
          }];
        }

        return module;
      });
    },
  },
  methods: {
    expiredLicenses() {
      return 0;
    },
    isLicenseExpired() {
      return null;
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-entries {
  @include vr($margin-top: 2);
  transition: opacity .15s ease-in-out;
}
</style>
