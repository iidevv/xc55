<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div>
    <div :data-loading="loading">
      <div
        v-if="!loading && totalCount > 0"
        class="upgrade-page page-wrapper">
        <upgrade-changelog-dialog/>
        <upgrade-preview
          v-for="(upgrades, upgradeType) in getAvailUpgradesByTypes"
          :key="upgradeType"
          :type="upgradeType"
          :upgrades="upgrades"
          :has-core-upgrade="hasAvailCoreUpgrade"/>
      </div>
      <empty-upgrade v-else-if="!loading && totalCount === 0"/>
    </div>
    <div
      v-if="loading"
      :data-loading-animation="loading"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import UpgradePreview from '../upgrade/UpgradePreview';
import EmptyUpgrade from '../upgrade/EmptyUpgrade';
import UpgradeChangelogDialog from '../dialogs/UpgradeChangelogDialog';

export default {
  name: 'UpgradePage',
  components: {
    UpgradePreview,
    EmptyUpgrade,
    UpgradeChangelogDialog,
  },
  computed: {
    ...mapState({
      totalCount(state) {
        return state.upgrades.count;
      },
      loading(state) {
        return state.upgrades.loading;
      },
      getAvailUpgradesByTypes(state) {
        if (Object.keys(state.upgrades.upgrade).length === 1) {
          this.$router.push(`/upgrade/${Object.keys(state.upgrades.upgrade)[0]}`);
        }
        return state.upgrades.upgrade;
      },
      hasAvailCoreUpgrade(state) {
        return Object.keys(_.filter(
          state.upgrades.upgrade,
          item => item.moduleId === 'CDev-Core')).length > 0;
      },
    }),
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-preview {
  h2 {
    display: flex;
    align-items: center;
    @include vr($height: 2);
    margin-bottom: 0;

    svg:not(:root).icon[data-inline] {
      @include vr($height: 1.167, $width: 1.375, $line-height: 1.167, $margin-right: 0.5);
    }
  }
}
</style>
