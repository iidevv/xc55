<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div
    v-if="hasData"
    class="upgrade-preview">
    <h2 v-if="hasModulesNotInMerchantWave">
      <icon
        :inline="true"
        :colorable="false"
        name="upgradeNoteWarning"/>
      <span v-html="titleWithWaveName"/>
    </h2>
    <h2
      v-else
      v-html="title"/>

    <i18n
      :path="changelogMessage"
      class="changelog-message">
      <i18n
        path="upgrade-preview.changelog-link"
        tag="a"
        href="#"
        @click.prevent="showChangelog"/>
    </i18n>

    <div class="upgrade-preview-entries">
      <upgrade-preview-entry
        v-for="entry in entries"
        :key="entry.id"
        :entry="entry"/>
    </div>

    <router-link
      :class="actionClasses"
      :to="pagePath"
      tag="button"
      class="regular-button">
      <i18n :path="buttonTitle"/>
    </router-link>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import UpgradePreviewEntry from './UpgradePreviewEntry';

export default {
  name: 'UpgradePreview',
  components: {
    UpgradePreviewEntry,
  },
  props: {
    upgrades: {
      type: Array,
      default: () => [],
    },
    type: {
      type: String,
      default: 'minor',
    },
    hasCoreUpgrade: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    ...mapState({
      waves(state) {
        return state.upgrades.waves;
      },
    }),
    entries() {
      return this.upgrades;
    },
    hasData() {
      return this.upgrades && this.upgrades.length;
    },
    title() {
      const label = this.coreIsInEntries
        ? `upgrade-preview.title.with-core.${this.type}`
        : `upgrade-preview.title.${this.type}`;
      return this.$tc(label, this.addonsCount, [this.addonsCount]);
    },
    titleWithWaveName() {
      const modulesMinWaveId = this.modulesMinWaveId;
      const wave = _.find(this.waves, upgradeWave => upgradeWave.id === modulesMinWaveId);

      if (!wave && modulesMinWaveId >= 0) {
        return this.title;
      }

      const waveName = wave
        ? wave.name
        : 'Tester';

      return `${this.title}. ${this.$t('upgrade-details-page.wave-warning.title', [waveName])}`;
    },
    changelogMessage() {
      return `upgrade-preview.changelog-message.${this.type}`;
    },
    buttonTitle() {
      return `upgrade-preview.button-title.${this.type}`;
    },
    pagePath() {
      return `/upgrade/${this.type}`;
    },
    actionClasses() {
      return {
        'regular-main-button': this.type === 'minor',
      };
    },
    coreIsInEntries() {
      const core = this.entries.filter(
        entry => entry.id === 'CDev-Core',
      );
      return core.length > 0;
    },
    addonsCount() {
      return this.coreIsInEntries
        ? this.upgrades.length - 1
        : this.upgrades.length;
    },
    merchantWaveId() {
      return _.reduce(this.waves, (maxWaveId, wave) => Math.max(maxWaveId, wave.id), 0);
    },
    modulesMinWaveId() {
      return this.entries.reduce((minWaveId, item) =>
        Math.min(minWaveId, +item.wave), this.merchantWaveId);
    },
    hasModulesNotInMerchantWave() {
      return this.modulesMinWaveId < this.merchantWaveId;
    },
  },
  methods: {
    showChangelog () {
      this.$modal.show('upgrade-changelog-dialog', {
        title: `changelog-dialog.title.${this.type}`,
        entries: this.entries,
      });
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-preview {
  @include vr($margin-bottom: 1, $padding-left: 1.5, $padding-right: 1.5, $padding-bottom: 1);
  border: 1px solid $border;
  border-radius: 10px;

  &:hover {
    box-shadow: 0 6px 10px 1px transparentize($input-border-color, .5);
  }

  .upgrade-preview-entries {
    @include vr($height: 3, $margin-top: 1, $margin-bottom: 1);
    display: flex;
    flex-wrap: wrap;
    overflow: hidden;

    .upgrade-preview-entry {
      @include vr($margin-right: .25);
    }
  }
}
</style>
