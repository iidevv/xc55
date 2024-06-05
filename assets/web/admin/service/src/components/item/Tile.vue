<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownTarget, CheckEmptyScriptTag -->
<template>
  <div
    :class="classes"
    class="module tile">
    <module-link
      :module="module"
      style-class="module-wrapper">

      <a href="#">
        <div
          v-if="module.skinPreview"
          class="skin-preview">
          <img
            :src="module.skinPreview"
            :alt="module.name">
        </div>
        <div
          v-else
          class="image">
          <img
            v-if="module.listIcon"
            :src="module.listIcon"
            :alt="module.name"
            class="list-icon">
          <img
            v-else-if="module.icon"
            :src="module.icon"
            :alt="module.name">
          <img
            v-else
            :alt="module.name"
            src="../../stylesheets/addon_default.png">
        </div>
      </a>

      <div class="main">
        <div class="name">{{ module.moduleName }}</div>
      </div>

      <div class="info">
        <div
          v-if="!isCore()"
          class="params">
          <i18n
            v-if="module.state === MODULE_STATES.ENABLED"
            path="Enabled"
            class="state enabled"/>
          <i18n
            v-else
            path="Disabled"
            class="state disabled"/>
        </div>
      </div>

      <div
        class="reveal-wrapper"
        tabindex="-1">
        <div class="reveal-collapsed">
          <module-alert
            v-if="hasWarning"
            :warning="module.warning"/>
          <div
            v-if="module.skinPreview"
            class="skin-preview">
            <img
              :src="module.skinPreview"
              :alt="module.name">
          </div>
          <div
            v-if="!module.skinPreview"
            class="name">
            {{ module.moduleName }}
          </div>
          <div
            v-if="!module.skinPreview"
            class="description">
            {{ module.description }}
          </div>
        </div>
      </div>
      <div
        class="actions-wrapper">
        <installed-module-actions
          :module="module"
          :is-tile="true"
        />
      </div>
    </module-link>
  </div>
</template>

<script>
import InstalledModuleActions from './action/InstalledModuleActions';
import ModuleLink from './ModuleLink';
import ModuleAlert from './ModuleAlert';
import { MODULE_STATES } from '../../../src/constants';

export default {
  components: {
    InstalledModuleActions,
    ModuleLink,
    ModuleAlert,
  },
  props: {
    module: {
      type: Object,
      default: () => {
      },
    },
    showActions: {
      type: Boolean,
      default: true,
    },
    showOnSale: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      MODULE_STATES: Object.freeze(MODULE_STATES),
    };
  },
  computed: {
    hasWarning() {
      return !_.isEmpty(this.module.warning);
    },
    classes() {
      return {
        'state-enabled': this.module.enabled || this.isCore(),
        'state-disabled': !this.module.enabled && !this.isCore(),
        'state-remove': this.module.scenarioState === MODULE_STATES.REMOVED,
        warning: this.hasWarning,
      };
    },
  },
  methods: {
    isCore() {
      return this.module.id === 'CDev-Core';
    },
    formatDate(timestamp) {
      return this.$d(new Date(timestamp * 1000), 'short');
    },
  },
};
</script>

<style lang="scss" src="../../stylesheets/tile.scss"></style>
