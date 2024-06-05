<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="restricted-modules-section">
    <modules-list
      v-if="count > 0"
      :modules="modules"
      :type="type"
      :show-actions="showActions"
    />
    <empty-modules-list
      v-else-if="count === 0 && !loading"
      :show-back-button="showBackButton"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';

import ModulesList from './ModulesList';
import EmptyModulesList from './EmptyModulesList';

export default {
  name: 'ModulesSection',
  components: {
    ModulesList,
    EmptyModulesList,
  },
  props: {
    namespace: {
      type: String,
      default: '',
    },
    filter: {
      type: Object,
      default: () => {
      },
    },
    type: {
      type: String,
      default: 'tile',
    },
    showActions: {
      type: Boolean,
      default: true,
    },
    showBackButton: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    ...mapState({
      modules(state) {
        return state.modulesData.modules;
      },
      actualFilter(state) {
        return state.modulesData.filter;
      },
      loading(state) {
        return state.modulesData.loading;
      },
      count(state) {
        return state.modulesData.count;
      },
      transitions(state) {
        return state.scenarios.common.transitions;
      },
    }),
    defaultFilter() {
      return {
        page: 1,
        language: this.locale,
        limit: 24,
      };
    },
    locale() {
      return this.$i18n.locale;
    },
  },
  watch: {
    actualFilter: {
      deep: true,
      handler(filter, oldFilter) {
        if (_.isEqual(filter, oldFilter)) {
          return;
        }
        this.fetchModulesWithShade();
      },
    },
    locale() {
      this.fetchModulesWithShade();
    },
  },
  beforeMount() {
    this.fetchModulesWithShade();
  },
  methods: {
    fetchModulesWithShade() {
      this.$store.dispatch(
        'modulesData/fetchModules',
        {
          transitions: this.transitions,
          shade: true,
        },
      );
    },
    fetchModulesImperceptibly() {
      this.$store.dispatch(
        'modulesData/fetchModules',
        {
          transitions: this.transitions,
          shade: false,
        },
      );
    },
  },
};
</script>
