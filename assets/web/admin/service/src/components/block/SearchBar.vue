<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div
    v-if="enabled"
    class="app-search-bar">
    <button
      class="search-button"
      @click="searchStringInputHandler">
      <icon
        name="search"
        width="20"
        height="20" />
    </button>
    <input
      ref="searchInput"
      v-model="currentSearchFilter"
      :placeholder="$t('Search in My Apps')"
      type="text"
      @keyup.13="searchStringInputHandler">
    <button
      :class="isInputEmpty ? 'hidden':''"
      class="clear-search-button"
      @click="clearClickHandler">
      <i class="fa-times" />
    </button>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import RoutingHelpers from '../mixins/RoutingHelpers';
import Icon from './Icon';

export default {
  name: 'SearchBar',
  components: { Icon },
  mixins: [RoutingHelpers],
  data() {
    return {
      currentSearchFilter: '',
    };
  },
  computed: {
    ...mapState({
      actualFilter(state) {
        return state.modulesData.filter;
      },
      transitions(state) {
        return state.scenarios.common.transitions;
      },
    }),
    enabled() {
      return this.$route.path.substring(1).split('/')[0] !== 'upgrade';
    },
    isInputEmpty() {
      return this.currentSearchFilter === '';
    },
  },
  beforeMount() {
    if (!_.isEmpty(this.searchQuery())) {
      this.currentSearchFilter = this.searchQuery().search;
    }
  },
  methods: {
    searchStringInputHandler() {
      if (this.currentSearchFilter) {
        this.$router.push({ path: '/' });

        const searchQuery = { search: this.currentSearchFilter };

        this.queryPatch(searchQuery);

        const newQuery = Object.assign(this.actualFilter, { page: 1, ...searchQuery });

        this.$store.commit('modulesData/SET_FILTER', newQuery);

        this.$store.dispatch(
          'modulesData/fetchModules',
          {
            transitions: this.transitions,
            shade: true,
          },
        );

        this.$refs.searchInput.blur();
      } else {
        this.queryPatch({}, ['search']);
      }
    },
    searchQuery() {
      return _.pick(this.$route.query, ['search']) || {};
    },
    clearClickHandler() {
      this.currentSearchFilter = '';

      this.queryPatch({}, ['search']);

      const newQuery = Object.assign(this.actualFilter, { search: null });

      this.$store.commit('modulesData/SET_FILTER', newQuery);

      this.$store.dispatch(
        'modulesData/fetchModules',
        {
          transitions: this.transitions,
          shade: true,
        },
      );
    },
  },
};
</script>

<style lang="scss">
  @import '../../stylesheets/common';

  .app-search-bar {
    position: relative;
    margin-left: $margin-base * 1.5;
    margin-right: $margin-base * 1.5;
    display: flex;
    align-items: center;

    input:not([type=radio]):not([type=checkbox]) {
      width: 270px;
      text-overflow: ellipsis;
      overflow: hidden;
      margin: 0;
      padding: ($input-padding-vertical * .5)
        ($input-padding-horizontal * 1.5)
        ($input-padding-vertical * .5)
        $input-padding-horizontal;
      height: $input-height-base;
      line-height: $input-height-base;
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      z-index: 2;
      color: $white-color;
      background: transparent;

      &::placeholder {
        color: $light-gray-color;
      }
    }

    .clear-search-button {
      appearance: none;
      padding: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: absolute;
      right: 0;
      top: 50%;
      z-index: 10;
      transform: translateY(-50%);
      background: transparent;
      border: 0;

      i {
        &:before {
          line-height: 1;
          font-size: 13px;
        }
      }
    }

    .search-button {
      appearance: none;
      background: transparent;
      border: 0;
      padding: 0;
      width: 1.5 * $rhythmic-unit;
      height: 1.5 * $rhythmic-unit;
      display: flex;
      align-items: center;
      justify-content: center;

      i {
        &:before {
          font-size: 20px;
        }
      }
    }

    @media (max-width: 900px) {
      & {
        margin-right: $margin-base / 2;
      }

      input:not([type=radio]):not([type=checkbox]) {
        width: auto;
      }
    }
  }
</style>
