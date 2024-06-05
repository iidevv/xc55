<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div>
    <div
      :class="type"
      class="modules-list">
      <component
        v-for="module in modules"
        :is="getComponent(module)"
        :key="module.id"
        :module="getConstructedModule(module)"
      />
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

import Tile from '../item/Tile';
import Row from '../item/Row';
import UpgradeModule from '../item/UpgradeModule';
import { getModuleWithIcons } from '../../utils';

export default {
  name: 'ModulesList',
  components: {
    Tile,
    Row,
    UpgradeModule,
  },
  props: {
    modules: {
      type: Array,
      default: () => [],
    },
    type: {
      type: String,
      default: 'tile',
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
  computed: {
    ...mapState({
      actualFilter: state => state.modulesData.filter,
    }),
  },
  methods: {
    getComponent(module) {
      return module.type === 'fake' ? 'fake-module' : this.type;
    },
    getConstructedModule(module) {
      return getModuleWithIcons(module);
    },
  },
};
</script>

<style lang="scss" src="../../stylesheets/modulesList.scss"></style>
