<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="empty-modules-list">
    <i18n
      v-if="actualFilter.search"
      path="header.addons_search_subtitle"
      tag="div"
      class="search-title">
      <span class="search-term">
        "{{ actualFilter.search }}"
      </span>
    </i18n>
    <icon
      name="noResult"
      height="200px"
      width="320px"/>
    <h2>
      {{ $t('modules-page.marketplace.empty.message') }}
    </h2>
    <p>
      {{ $t('modules-page.marketplace.empty.search-tips') }}
    </p>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'EmptyModulesList',
  props: {
    showBackButton: {
      default: true,
      type: Boolean,
    },
  },
  computed: {
    ...mapState({
      actualFilter: state => state.modulesData.filter,
    }),
  },
};
</script>

<style lang="scss" scoped>
  @import '../../stylesheets/common';

  .fade-enter-active .empty-modules-list,
  .fade-enter .empty-modules-list,
  .fade-leave-active .empty-modules-list,
  .fade-leave .empty-modules-list,
  .modules-list-block[data-loading] .empty-modules-list {
    opacity: 0;
  }

  .empty-modules-list {
    opacity: 1;
    transition: $basic-transition;
    display: flex;
    height: 100%;
    width: 100%;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;

    h2 + p {
      margin-top: 0;
    }

    .search-title {
      align-self: flex-start;
    }
  }
</style>
