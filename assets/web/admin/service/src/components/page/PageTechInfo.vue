<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div
    v-if="loaded"
  >
    <store-info-block
      :store-info="storeInfo"
    />
    <tech-info-modules-list
      :columns="modulesColumns"
      :rows="privateModules"
      title="tech-info.private-modules"
      list-empty-label="tech-info.no-private-modules"
      no-search-results-label="tech-info.no-private-modules-found"
    />
    <tech-info-modules-list
      :columns="modulesColumns"
      :rows="publicModules"
      title="tech-info.public-modules"
      list-empty-label="tech-info.no-public-modules"
      no-search-results-label="tech-info.no-public-modules-found"
    />
  </div>
  <div
    v-else
    :data-loading-animation="true"/>

</template>

<script>
import { mapState } from 'vuex';
import StoreInfoBlock from '../block/StoreInfoBlock';
import TechInfoModulesList from '../list/TechInfoModulesList';

export default {
  name: 'PageTechInfo',
  components: { StoreInfoBlock, TechInfoModulesList },
  mixins: [],
  computed: {
    ...mapState({
      storeInfo: state => state.techInfo.store,
      loaded: state => state.techInfo.loaded,
      privateModules: state => (
        state.techInfo.modules.private
          ? state.techInfo.modules.private
          : []
      ),
      publicModules: state => (
        state.techInfo.modules.public
          ? state.techInfo.modules.public
          : []
      ),
    }),
  },
  beforeMount() {
    this.$store.dispatch('techInfo/fetchStoreInfo');
    this.modulesColumns = [
      {
        type: 'string',
        title: 'tech-info.developer',
        field: 'author',
      },
      {
        type: 'string',
        title: 'tech-info.developer',
        field: 'name',
      },
      {
        type: 'string',
        title: 'tech-info.version',
        field: 'version',
      },
      {
        type: 'date',
        title: 'tech-info.install-date',
        field: 'enabled_timestamp',
      },
      {
        type: 'string',
        title: 'tech-info.description',
        field: 'description',
      },
    ];
  },
};
</script>
