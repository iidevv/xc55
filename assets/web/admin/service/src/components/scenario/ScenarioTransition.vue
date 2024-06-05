<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="transition">
    <a
      :class="transitionStateToSet"
      href="javascript:void(0)"
      class="transition-module"
      @click="clickHandler(moduleId)">
      {{ moduleName }}
    </a>
    <span class="transition-type">
      {{ $t('scenario.transition.stateToSet.' + transitionStateToSet) }}
    </span>
  </div>
</template>

<script>
export default {
  name: 'ScenarioTransition',
  props: {
    moduleId: {
      type: String,
      default: '',
    },
    moduleName: {
      type: String,
      default: '',
    },
    transitionStateToSet: {
      type: String,
      default: '',
    },
  },
  methods: {
    clickHandler(moduleId) {
      this.$store.commit('singleModule/SET_ID', moduleId);
      this.$store.dispatch('singleModule/fetchModuleData', { stateToSet: this.transitionStateToSet });
    },
  },
};
</script>
