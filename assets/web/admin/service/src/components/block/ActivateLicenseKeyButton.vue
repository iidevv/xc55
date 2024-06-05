<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <button
    v-if="!isDemoMode"
    class="btn regular-button activate-license-key-button"
    @click="clickHandler">
    <i18n path="Activate License Key"/>
  </button>
</template>

<script>
import { mapState } from 'vuex';
import appConfig from '../../../config/app-config';

export default {
  name: 'ActivateLicenseKeyButton',
  computed: {
    ...mapState({
      licenses(state) {
        return state.licenses;
      },
    }),
    isTrial() {
      return this.licenses.coreInfo.isTrial;
    },
    title() {
      return this.isTrial ? this.$t('License key registration') : this.$t('Enter license key');
    },
    isDemoMode() {
      return appConfig.isDemoMode;
    },
  },
  beforeMount() {
    this.$store.dispatch('licenses/fetchCoreInfo');
  },
  methods: {
    clickHandler() {
      this.$modal.show('activate-license-key', {
        title: this.title,
      });
    },
  },
};
</script>

<style lang="scss">
  @import '../../stylesheets/common';

  .activate-license-key-button {
    margin-right: $rhythmic-unit;;
  }
</style>
