<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="scenario-section minicart__dropdown--content">
    <div class="title">
      <span class="text">
        <i18n path="Selected modules"/>
        ({{ transitionsCount }})
        <i18n path="will be"/>
      </span>
    </div>
    <div
      ref="minicartListWrapper"
      :class="shadowClass"
      class="minicart__dropdown--content--list">
      <ol ref="minicartList">
        <li
          v-for="(transition, index) in transitions"
          :key="transition.moduleName">
          <scenario-transition
            :module-id="index"
            :module-name="transition.moduleName"
            :transition-state-to-set="transition.stateToSet"/>
        </li>
      </ol>
    </div>
    <div
      :class="shadowClass"
      class="run-scenario-wrapper">
      <run-rebuild-button :type="type"/>
      <a
        class="discard"
        href="#"
        @click="discard">
        <i18n path="Clear all"/>
      </a>
    </div>
  </div>
</template>

<script>
import ScenarioTransition from './ScenarioTransition';
import RunRebuildButton from '../block/RunRebuildButton';

export default {
  name: 'ScenarioSection',
  components: {
    RunRebuildButton,
    ScenarioTransition,
  },
  props: {
    discard: {
      type: Function,
      default() {
        return false;
      },
    },
    open: {
      type: Boolean,
      default() {
        return false;
      },
    },
    transitions: {
      type: Object,
      default() {
        return {};
      },
    },
    type: {
      type: String,
      default() {
        return 'common';
      },
    },
  },
  data() {
    return {
      hasShadow: false,
    };
  },
  computed: {
    transitionsCount() {
      return Object.keys(this.transitions).length;
    },
    shadowClass() {
      return {
        'has-shadow': this.hasShadow,
      };
    },
  },
  watch: {
    open() {
      if (
        !this.hasShadow
        && Object.keys(this.$refs)
        && this.$refs.minicartList.clientHeight > 0
        && this.$refs.minicartListWrapper.clientHeight > 0
      ) {
        this.hasShadow = this.detectOverflow();
      }
    },
  },
  mounted() {
    this.hasShadow = this.detectOverflow();
  },
  updated() {
    this.hasShadow = this.detectOverflow();
  },
  methods: {
    detectOverflow() {
      return this.$refs.minicartList.clientHeight > this.$refs.minicartListWrapper.clientHeight;
    },
  },
};
</script>
