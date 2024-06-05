<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--
events:
    - selectForUpgradeEvent
-->

<template>
  <div
    v-if="advanced"
    class="upgrade-module actions">
    <button
      v-if="advanced"
      class="select-for-upgrade"
      @click.prevent.stop="selectForUpgradeHandler()">
      <checkbox :checked="selected"/>
      <i18n
        v-if="$route.params.type === 'minor'"
        path="module_action.upgrade_addon.minor"/>
      <i18n
        v-else-if="$route.params.type === 'major'"
        path="module_action.upgrade_addon.major"/>
    </button>
  </div>

</template>

<script>
import { mapState } from 'vuex';
import Checkbox from '../../input/Checkbox';

export default {
  name: 'UpgradeModuleActions',
  components: {
    Checkbox,
  },
  props: {
    module: {
      type: Object,
      default: () => {},
    },
    selected: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    ...mapState({
      advanced(state) {
        return state.upgrades.advancedMode;
      },
    }),
  },
  methods: {
    selectForUpgradeHandler() {
      this.$store.commit('upgrades/SET_ENTRY_ADVANCED_STATE', {
        id: this.module.id,
        value: !this.selected,
      });

      this.$emit('selectForUpgradeEvent', this.module, !this.selected);
    },
  },
};
</script>

<style lang="scss">
@import '../../../stylesheets/common';

.upgrade-module.actions {
  z-index: 1;
  width: 100%;

  .select-for-upgrade {
    width: 100%;
    background: #fff;
    border-radius: $btn-border-radius;
    border: $btn-border-width solid $btn-regular-border-color;
    color: $link;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: $btn-padding-vertical $btn-padding-horizontal;
  }
}
</style>
