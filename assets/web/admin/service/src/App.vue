<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div
    id="app"
    class="app-page-container">
    <router-view name="header"/>
    <router-view/>
    <activate-license-key-dialog/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import ActivateLicenseKeyDialog from './components/dialogs/ActivateLicenseKeyDialog';
import { upgradeNoteControl } from './utils';

export default {
  name: 'App',
  components: {
    ActivateLicenseKeyDialog,
  },
  computed: {
    ...mapState({
      hasUpgrades(state) {
        return state.upgrades.count > 0;
      },
    }),
  },
  beforeMount() {
    this.$store.dispatch('upgrades/fetchAvailableUpgrades', { shade: true });
    this.$store.dispatch('upgrades/fetchWavesCommonInfo');

    upgradeNoteControl(this.hasUpgrades);
  },
  updated() {
    upgradeNoteControl(this.hasUpgrades);
  },
};
</script>

<style lang="scss">
.app-page-container {
  min-width: 750px;
}
</style>
