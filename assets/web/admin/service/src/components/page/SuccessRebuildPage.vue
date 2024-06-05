<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="success">
    <div class="success-image">
      <icon
        name="successUpgrade"
        height="200px"
        width="290px"/>
    </div>

    <i18n
      path="success-upgrade.title"
      tag="h1"/>

    <div
      class="message"
      v-html="message()" />

    <div class="actions">
      <i18n
        :href="storefrontUrl()"
        path="success-upgrade.storefront"
        tag="a"
        class="button regular-main-button"
        target="_blank"/>

      <i18n
        :href="adminUrl()"
        :path="buttonTitle()"
        tag="a"
        class="button regular-main-button"
        target="_blank"/>
    </div>

    <i18n
      path="success-upgrade.description"
      tag="div"
      class="description"/>
  </div>
</template>

<script>
import appConfig from '../../../config/app-config';

export default {
  name: 'SuccessRebuildPage',
  data() {
    return {
      enabledModules: [],
      enabledModulesWithSettings: [],
    };
  },
  computed: {
    hasEnabledModules() {
      return this.enabledModules.length > 0;
    },
  },
  beforeMount() {
    this.enabledModules = JSON.parse(sessionStorage.getItem('enabledModules')) || [];
    this.enabledModulesWithSettings = JSON.parse(sessionStorage.getItem('enabledModulesWithSettings')) || [];

    this.$cookies.set('rebuild_success', true);
  },
  methods: {
    adminUrl() {
      if (this.enabledModulesWithSettings.length === 1) {
        return `${appConfig.url}/${appConfig.adminScript}/?target=module&moduleId=${this.enabledModulesWithSettings[0]}`;
      }

      if (this.enabledModulesWithSettings.length > 1) {
        return `${appConfig.url}/${appConfig.adminScript}/?target=apps#/installed-addons?sortBy=recent`;
      }

      return `${appConfig.url}/${appConfig.adminScript}/`;
    },
    storefrontUrl() {
      return appConfig.url;
    },
    message() {
      const sortByRecentUrl = `${appConfig.url}/${appConfig.adminScript}/?target=apps#/installed-addons?sortBy=recent`;
      const message = this.enabledModulesWithSettings.length > 0
        ? this.$t('success-upgrade.installed-message')
        : this.$t('success-upgrade.installed-message-link', [sortByRecentUrl]);

      return this.hasEnabledModules ? message : this.$t('success-upgrade.message');
    },
    buttonTitle() {
      const modulesWithSettingsButtonTitle = this.enabledModulesWithSettings.length === 1
        ? 'success-upgrade.installed-addon'
        : 'success-upgrade.installed-addons';

      return this.enabledModulesWithSettings.length > 0
        ? modulesWithSettingsButtonTitle
        : 'success-upgrade.admin-area';
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.success {
  display: flex;
  flex-direction: column;
  align-items: center;

  .success-image,
  .actions {
    @include vr($margin-top: 1, $margin-bottom: 1);
  }

  .actions {
    display: flex;

    .button + .button {
      @include vr($margin-left: 1);
    }
  }

  .description {
    @include vr($margin-top: 1, $margin-bottom: 2);
    width: 80%;
    text-align: center;
  }
}
</style>
