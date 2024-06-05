<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag, HtmlUnknownAttribute -->
<template>
  <div>
    <div
      v-if="entries.length"
      :data-loading="!loaded"
      class="upgrade-details-page page-wrapper">
      <upgrade-changelog-dialog/>
      <module-changelog-dialog/>
      <activate-license-dialog/>

      <div class="advanced-mode-button">
        <checkbox
          v-if="entries.length > 1"
          :checked="advancedMode"
          :disabled="!isUpgradeAvailable"
          type="lock"
          @change="toggleAdvancedMode">
          <i18n
            v-if="advancedMode"
            path="upgrade-details-page.advanced-mode.enabled"/>
          <i18n
            v-else
            path="upgrade-details-page.advanced-mode.disabled"/>
        </checkbox>
      </div>

      <div
        v-if="advancedMode"
        class="advanced-mode-message">
        <i18n
          path="upgrade.advanced_mode.message"
          tag="div"
          class="advanced-mode-message-title"/>
        <i18n
          path="upgrade.advanced_mode.warning.message"
          tag="div"/>
      </div>

      <div class="changelog-message">
        <i18n
          :path="changelogMessage"
          tag="div">
          <i18n
            path="upgrade-preview.changelog-link"
            tag="a"
            href="#"
            @click.prevent="showChangelog"/>
        </i18n>
        <i18n
          v-if="hasModulesNotInMerchantWave"
          path="upgrade-details-page.wave-warning.message"
          tag="div">
          <span
            class="wave-name-bold"
            v-html="waveName"/>
          <span v-html="modulesMinWaveName"/>
          <i18n
            :href="changeWaveURL"
            path="upgrade-details-page.wave-warning.message.link"
            tag="a"/>
        </i18n>
      </div>

      <block-alert
        v-if="hasUpgradesNotServerCompatible"
        message="upgrade-details-page.license-warning.missing"
        variant="warning"
        show>
        <div class="alert alert-warning">
          <i18n
            v-for="(errorText, index) in upgradesNotServerCompatible"
            :key="index"
            :path="errorText.label"
            tag="p">
            <strong
              v-for="(param, index) in errorText.params"
              :key="index">{{ param }}</strong>
          </i18n>
        </div>
      </block-alert>

      <block-alert
        v-if="hasCustomModules"
        message="upgrade-details-page.disabled-and-custom-modules-warning"
        variant="warning"
        show>
        <div class="alert alert-warning">
          <i18n
            path="upgrade-details-page.disabled-and-custom-modules-warning.title"
            tag="strong"/>
          <ul class="disabled-and-custom-module-list">
            <i18n
              v-if="hasCustomModules"
              path="upgrade-details-page.custom-modules-warning"
              tag="li">
              <router-link
                to="/custom-modules"
                tag="a">
                <i18n path="upgrade-details-page.custom-modules-warning.custom-modules"/>
              </router-link>
            </i18n>
          </ul>
        </div>
      </block-alert>

      <upgrade-entries :type="type"/>

      <block-alert
        v-if="isTrialEdition || isTrialAccessExpired"
        message="upgrade-details-page.license-warning.missing"
        variant="warning"
        show>
        <div class="alert alert-warning">
          <i18n
            path="upgrade-details-page.missing-license.message"
            tag="p"/>

          <i18n
            path="Activate license key"
            class="activate-license regular-main-button"
            tag="button"
            @click="activateLicenseClickHandler"/>
        </div>
      </block-alert>

      <block-alert
        v-if="disallowed.length > 0"
        message="upgrade-details-page.license-warning.missing"
        variant="warning">
        <div class="alert alert-warning">
          <i18n
            path="upgrade-details-page.missing-module-license.message"
            tag="div"/>

          <div class="expired-modules-list">
            <div
              v-for="(license, index) in disallowed"
              :key="index">
              <strong>{{ license.readableName }}</strong>
            </div>
          </div>

          <i18n
            path="Activate license key"
            class="activate-license regular-main-button"
            tag="button"
            @click="activateLicenseClickHandler"/>
        </div>
      </block-alert>

      <block-alert
        v-if="isEditionExpired || expiredLicenses.length > 0"
        message="upgrade-details-page.license-warning.expired"
        variant="warning"
        show>
        <div class="alert alert-warning">
          <i18n
            path="upgrade-details-page.expired-license.message"
            tag="div"/>

          <div class="expired-modules-list">
            <i18n
              v-for="(license, index) in expiredLicenses"
              :key="index"
              path="upgrade-details-page.expired-license.license"
              tag="div">
              <strong>{{ license.readableName }}</strong>
              <span v-html="formatDate(license.expiredAt)"/>
            </i18n>
          </div>

          <i18n
            path="Renew your access to new features"
            tag="button"
            class="activate-license regular-main-button"
            @click="renewLicensesHandler"/>

          <loading-button
            :is-loading="isLoading"
            class="re-validate-license regular-button"
            path="Re-validate license keys"
            @clickHandler="revalidateLicenseClickHandler"/>
        </div>
      </block-alert>

      <div
        v-if="isUpgradeAvailable"
        class="controls">
        <div class="consent">
          <div>
            <label class="backup-confirm">
              <input
                v-model="backupConfirm"
                type="checkbox">
              <i18n path="upgrade-details-page.backup-confirm"/>
            </label>
          </div>

          <div v-if="hasCustomModules && type === 'major'">
            <label>
              <input
                v-model="customModulesConfirm"
                type="checkbox">
              <i18n path="upgrade-details-page.custom-modules-confirm">
                <router-link
                  to="/custom-modules"
                  tag="a">
                  <i18n path="upgrade-details-page.custom-modules-confirm.custom-modules"/>
                </router-link>
              </i18n>
            </label>
          </div>
        </div>

        <div class="actions">
          <run-rebuild-button
            :disabled="!canContinue"
            type="upgrade">
            <i18n path="upgrade-details-page.continue"/>
          </run-rebuild-button>
          <label
            v-if="advancedMode"
            class="select-all">
            <input
              v-model="selectAll"
              type="checkbox"
              @click="selectAllForUpgradeHandler">
            <i18n path="upgrade.advanced_mode.select_all"/>
          </label>
        </div>
      </div>
    </div>
    <empty-upgrade v-else-if="!loading"/>
    <div
      v-if="!loaded"
      :data-loading-animation="!loaded"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import appConfig from '../../../config/app-config';
import UpgradeChangelogDialog from '../dialogs/UpgradeChangelogDialog';
import ModuleChangelogDialog from '../dialogs/ModuleChangelogDialog';
import ActivateLicenseDialog from '../dialogs/ActivateLicenseKeyDialog';
import BlockAlert from '../block/BlockAlert';
import UpgradeEntries from '../upgrade/UpgradeEntries';
import EmptyUpgrade from '../upgrade/EmptyUpgrade';
import Checkbox from '../input/Checkbox';
import RunRebuildButton from '../block/RunRebuildButton';
import LoadingButton from '../block/LoadingButton';
import { openTab, renewLicensesUrl } from '../../utils';

/* global define */
export default {
  name: 'UpgradeDetailsPage',
  components: {
    UpgradeChangelogDialog,
    ModuleChangelogDialog,
    ActivateLicenseDialog,
    BlockAlert,
    UpgradeEntries,
    EmptyUpgrade,
    Checkbox,
    RunRebuildButton,
    LoadingButton,
  },
  props: {
    moduleId: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      backupConfirm: false,
      removeDisabledConfirm: false,
      customModulesConfirm: false,
      selectAll: true,
      isLoading: false,
      activateKeyHandler: null,
      renewalsResult: null,
    };
  },
  computed: {
    ...mapState({
      xcart(state) {
        return state.xcart;
      },
      advancedMode(state) {
        return state.upgrades.advancedMode;
      },
      disallowed(state) {
        return state.upgrades.disallowed;
      },
      disallowedFetched(state) {
        return state.upgrades.disallowedFetched;
      },
      entries(state) {
        return (
          this.$route.params.type
          && state.upgrades.upgrade
          && state.upgrades.upgrade[this.$route.params.type]
        )
          ? this.fetchedEntries[this.$route.params.type]
          : [];
      },
      transitions(state) {
        return state.scenarios.common.transitions;
      },
      hasCustomModules(state) {
        return state.modulesData.customModulesCount > 0;
      },
      fetchedEntries(state) {
        return state.upgrades.upgrade;
      },
      hasAvailCoreUpgrade(state) {
        return Object.keys(_.filter(
          state.upgrades.upgrade,
          item => item.moduleId === 'CDev-Core')).length > 0;
      },
      licensesInfo(state) {
        return state.licenses;
      },
      loading(state) {
        return state.upgrades.loading;
      },
      loaded(state) {
        return state.upgrades.loaded
          && state.upgrades.disallowedFetched
          && (!state.modulesData.loading && state.modulesData.loaded);
      },
      waves(state) {
        return state.upgrades.waves;
      },
      unselected(state) {
        return state.upgrades.unselected;
      },
    }),
    type() {
      return this.$route.params.type;
    },
    isEditionExpired() {
      return !this.licensesInfo.coreInfo.isTrial
        && this.licensesInfo.coreInfo.isExpired;
    },
    isTrialAccessExpired() {
      return this.licensesInfo.coreInfo.isTrial
        && this.licensesInfo.coreInfo.isExpired;
    },
    isTrialEdition() {
      return this.licensesInfo.coreInfo.isTrial;
    },
    isUpgradeAvailable() {
      return !this.isTrialEdition
        && !this.isTrialAccessExpired
        && !this.upgradesWithoutLicense.length
        && !this.hasUpgradesNotServerCompatible
        && !(this.type === 'major' && this.currentTypeUpgradesWithExpiredLicense.length > 0);
    },
    isRootPublic() {
      return document.querySelector('.root-upgrade-block');
    },
    canContinue() {
      const customModulesConfirmCondition = (this.type === 'major' && this.hasCustomModules)
        ? this.customModulesConfirm
        : true;

      return !this.isRootPublic
        && this.backupConfirm
        && customModulesConfirmCondition;
    },
    changelogMessage() {
      return `upgrade-preview.changelog-message.${this.type}`;
    },
    coreVersion() {
      return this.licensesInfo.coreInfo.coreVersion;
    },
    licenses() {
      return this.licensesInfo.info;
    },
    expiredLicenses() {
      const modules = [];

      _.filter(this.licenses, (license) => {
        if (license.isExpired) {
          modules.push(license);
        }
      });

      return modules;
    },
    currentTypeUpgrades() {
      return _.filter(
        this.entries,
        entry => this.isCurrentTypeUpgrade(entry),
      );
    },
    upgradesWithoutLicense() {
      return _.filter(
        this.entries,
        entry => _.some(
          this.disallowed,
          disallowedModule => entry.id === `${disallowedModule.author}-${disallowedModule.name}`,
        ),
      );
    },
    upgradesNotServerCompatible() {
      return _.pluck(
        _.filter(
          this.currentTypeUpgrades,
          upgrade => Object.keys(upgrade.incompatibleError).length > 0,
        ),
        'incompatibleError');
    },
    hasUpgradesNotServerCompatible() {
      return this.upgradesNotServerCompatible.length > 0;
    },
    currentTypeUpgradesWithExpiredLicense() {
      return _.filter(
        this.currentTypeUpgrades,
        upgrade => _.some(
          this.expiredLicenses,
          expiredLicense => upgrade.id === expiredLicense.moduleId,
        ),
      );
    },
    merchantWaveId() {
      return _.reduce(this.waves, (maxWaveId, wave) => Math.max(maxWaveId, wave.id), 0);
    },
    modulesMinWaveId() {
      return this.entries.reduce(
        (minWaveId, item) => Math.min(minWaveId, +item.wave),
        this.merchantWaveId,
      );
    },
    hasModulesNotInMerchantWave() {
      return this.modulesMinWaveId < this.merchantWaveId;
    },
    waveName() {
      const wave = _.filter(this.waves, currentWave => currentWave.id === this.wave);

      return wave
        ? wave.name
        : 'Tester';
    },
    modulesMinWaveName() {
      const modulesMinWaveId = this.modulesMinWaveId;
      const wave = _.find(this.waves, currentWave => currentWave.id === modulesMinWaveId);

      return wave
        ? wave.name
        : 'Tester';
    },
    changeWaveURL() {
      return `${appConfig.url}/${appConfig.adminScript}?target=settings&page=Environment`;
    },
  },
  watch: {
    unselected(newState) {
      this.$store.dispatch('scenarios/upgrade/fillUpgradeScenario', { upgradeType: this.type });

      this.selectAll = (newState.length === 0);
    },
    loaded(newState) {
      if (newState === true) {
        this.$store.dispatch(
          'scenarios/upgrade/fillUpgradeScenario',
          { upgradeType: this.type },
          { root: true },
        );
      }
    },
  },
  created() {
    define('activateKeyHandler', ['common/activateKeyHandler'], (activateKeyHandler) => {
      this.activateKeyHandler = activateKeyHandler;
    });
  },
  destroyed() {
    this.$store.commit('upgrade/SET_ADVANCED_MODE', false);
  },
  beforeMount() {
    this.$store.dispatch('upgrades/fetchDisallowedModules');
    this.$store.dispatch('licenses/fetchCoreInfo');
    this.$store.dispatch('licenses/fetchLicenses');

    this.$store.dispatch('modulesData/fetchModules', {
      transitions: this.transitions,
      shade: true,
      fetchCustom: true,
    });

    this.renewalsResult = this.$route.params.renewalsResult;
  },
  updated() {
    if (this.renewalsResult) {
      this.notifyAboutRenewalsResult();

      this.renewalsResult = null;
    }
  },
  methods: {
    showChangelog() {
      this.$modal.show('upgrade-changelog-dialog', {
        title: `changelog-dialog.title.${this.type}`,
        entries: this.entries,
      });
    },
    toggleAdvancedMode(value) {
      this.$store.commit('upgrades/SET_ADVANCED_MODE', value);
    },
    activateLicenseClickHandler() {
      this.$modal.show('activate-license-key', {
        title: this.$t('License key registration'),
      });
    },
    renewLicensesHandler() {
      openTab(renewLicensesUrl(this.expiredLicenses));
    },
    revalidateLicenseClickHandler() {
      if (this.isLoading) {
        return;
      }

      this.isLoading = true;
      const promises = [];

      this.licenses.forEach((license) => {
        promises.push(
          this.activateKeyHandler().handle(
            { licenseKey: license.keyValue },
            true,
          ),
        );
      });

      Promise.all(promises).then(() => {
        this.xcart.trigger(
          'message',
          {
            type: 'info',
            message: this.$t('upgrade-details-page.licenses.updated'),
          },
        );

        this.$store.dispatch('upgrades/fetchDisallowedModules');
        this.$store.dispatch('licenses/fetchCoreInfo');
        this.$store.dispatch('licenses/fetchLicenses');

        this.isLoading = false;
      });
    },
    selectAllForUpgradeHandler() {
      this.entries.forEach((item) => {
        this.$store.commit('upgrades/SET_ENTRY_ADVANCED_STATE', {
          id: item.id,
          value: !this.selectAll,
        });

        this.$emit('selectForUpgradeEvent', item, !this.selectAll);
      });
    },
    formatDate(timestamp) {
      return this.$d(new Date(timestamp * 1000), 'short');
    },
    notifyAboutRenewalsResult() {
      const params = this.renewalsResult === 'success'
        ? { type: 'info', message: this.$t('success-renewal.message') }
        : { type: 'error', message: this.$t('failure-renewal.message') };

      this.xcart.trigger('message', params);
    },
    isCurrentTypeUpgrade(entry) {
      // entry.type "build" corresponds to "minor" page type
      // entry.type "minor" and "major" corresponds to "major" page type
      return (
        (this.type === 'minor' && entry.type === 'build')
        || (this.type !== 'minor' && entry.type !== 'build')
      );
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.upgrade-details-page {
  .advanced-mode-button {
    @include vr($height: 2);
    align-self: flex-end;
    display: flex;
    justify-content: flex-end;

    label.text span {
      @include vr($line-height: 2);
    }
  }

  .advanced-mode-message {
    @include vr($margin-bottom: 1);

    .advanced-mode-message-title {
      font-weight: $font-weight-bold;
    }
  }

  .disabled-and-custom-module-list {
    @include vr($margin: 0 0 0 .85);
    padding: 0;
  }

  .changelog-message {
    @include vr($margin-bottom: 1);

    .wave-name-bold {
      font-weight: bold;
    }
  }

  .alert-wrapper {
    @include vr($margin-bottom: .5);
  }

  .re-validate-license {
    @include vr($margin-left: 1);
  }

  .module-section {
    @include vr($margin-top: 1, $margin-bottom: 2.5);
  }

  .controls {
    @include vr($margin-top: .5);

    .consent {
      & > div {
        @include vr($margin-bottom: .5);

        .need-help-backup {
          @include vr($margin-left: 1);
        }
      }
    }

    .actions {
      display: flex;
      align-items: center;

      .select-all {
        @include vr($margin-left: .5);
        display: flex;
        align-items: center;
      }
    }
  }

  .backup-confirm {
    display: flex;
    align-items: center;
  }

  .expired-modules-list {
    margin-bottom: .5 * $rhythmic-unit;
  }
}
</style>
