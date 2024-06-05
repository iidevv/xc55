<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <button
    :disabled="isButtonDisabled"
    class="regular-button regular-main-button run-scenario"
    @click="clickHandler">
    <i18n path="Apply changes"/>
  </button>
</template>

<script>
import { mapState } from 'vuex';
import appConfig from '../../../config/app-config';

export default {
  name: 'RunRebuildButton',
  props: {
    disabled: {
      type: Boolean,
      default() {
        return false;
      },
    },
    type: {
      type: String,
      default() {
        return 'common';
      },
    },
  },
  computed: {
    ...mapState({
      transitions(state) {
        return state.scenarios[this.type].transitions;
      },
    }),
    hasData() {
      return this.transitions && Object.keys(this.transitions).length > 0;
    },
    isButtonDisabled() {
      return this.disabled || !this.hasData;
    },
  },
  methods: {
    clickHandler() {
      // Save some config data for the rebuild page
      sessionStorage.setItem('xcUrl', appConfig.url);

      this.$store.dispatch(`scenarios/${this.type}/rebuild`);
    },
  },
};
</script>
