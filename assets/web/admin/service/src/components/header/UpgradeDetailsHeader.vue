<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="page-header">
    <h1
      v-if="hasModulesNotInMerchantWave"
      class="upgrade-details-header">
      <icon
        :inline="true"
        :colorable="false"
        name="upgradeNoteWarning"/>
      <span v-html="titleWithWaveName"/>
    </h1>
    <h1
      v-else
      class="upgrade-details-header"
      v-html="headerTitle"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Icon from '../block/Icon';
import PageHeader from './PageHeader';

export default {
  name: 'UpgradeDetailsHeader',
  components: {
    Icon,
  },
  extends: PageHeader,
  computed: {
    ...mapState({
      fetchedEntries(state) {
        return state.upgrades.upgrade;
      },
      waves(state) {
        return state.upgrades.waves;
      },
      coreVersion(state) {
        return state.licenses.coreInfo.coreVersion;
      },
    }),

    type() {
      return this.$route.params.type;
    },
    entries() {
      return this.type && this.fetchedEntries && this.fetchedEntries[this.type]
        ? this.fetchedEntries[this.type]
        : [];
    },
    headerTitle() {
      return this.$t(`upgrade-details-title.${this.type}`, [this.coreVersion]);
    },
    merchantWaveId() {
      return _.reduce(this.waves, (maxWaveId, wave) => Math.max(maxWaveId, wave.id), 0);
    },
    titleWithWaveName() {
      const modulesMinWaveId = this.modulesMinWaveId;

      const currentWave = _.find(
        this.waves,
        wave => wave.id === modulesMinWaveId);

      if (!currentWave && modulesMinWaveId >= 0) {
        return this.headerTitle;
      }

      const waveName = currentWave
        ? currentWave.name
        : 'Tester';

      return `${this.headerTitle}. ${this.$t('upgrade-details-page.wave-warning.title', [waveName])}`;
    },
    modulesMinWaveId() {
      const returns = this.entries.reduce(
        (minWaveId, item) => Math.min(minWaveId, +item.wave),
        this.merchantWaveId,
      );

      return returns;
    },
    hasModulesNotInMerchantWave() {
      return this.modulesMinWaveId < this.merchantWaveId;
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-details-header {
  display: flex;
  align-items: center;
  @include vr($height: 2);

  svg:not(:root).icon[data-inline] {
    @include vr($height: 1.167, $width: 1.375, $line-height: 1.167, $margin-right: 0.5);
    overflow: visible;
  }
}
</style>
