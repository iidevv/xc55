<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div class="modules-page">
    <div :data-loading="loading">
      <modules-section
        :type="type"
        :filter="pageFilter"
        namespace="page"/>
      <div
        v-if="count > 0"
        class="sticky-panel">
        <div class="table-pager">
          <div class="box">
            <pager
              v-if="pagesCount > 1"
              :total-pages="pagesCount"
              :current-page="currentPage"
              :per-page="perPage"
              @change="changePageHandler"/>

            <per-page-selector
              :current-page="currentPage"
              :current-per-page="perPage"
              :count="count"
              :items-per-page="itemsPerPage"/>

            <a
              v-tooltip.top="tooltipText"
              v-if="showDisplayModeSwitcher"
              :title="tooltipText"
              class="display-mode-switcher"
              @click="switchDisplayModeHandler">
              <icon
                :name="displayModeToSwitch"
                class="display-mode"/>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="loading"
      :data-loading-animation="loading"/>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Icon from '../block/Icon';
import ModulesSection from './ModulesSection';
import Pager from '../block/Pager';
import PerPageSelector from '../block/PerPageSelector';
import RoutingHelpers from '../mixins/RoutingHelpers';
import { ITEMS_PER_PAGE } from '../../constants';

export default {
  name: 'ModulesPage',
  components: {
    Icon,
    ModulesSection,
    Pager,
    PerPageSelector,
  },
  mixins: [RoutingHelpers],
  props: {
    emptyComponent: {
      type: String,
      default: '',
    },
    filter: {
      type: Object,
      default: () => {},
    },
    showDisplayModeSwitcher: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const pageName = this.$router.currentRoute.path.substring(1);
    const current = parseInt(this.$cookies.get(`current_page_${pageName}`), 10);

    return {
      defaultFilter: {
        limit: 24,
        page: (!isNaN(current) && typeof current === 'number') ? current : 1,
      },
      type: 'tile',
      itemsPerPage: ITEMS_PER_PAGE,
    };
  },
  computed: {
    ...mapState({
      actualFilter: state => state.modulesData.filter,
      loading: state => state.modulesData.loading,
      count: state => parseInt(state.modulesData.count, 10),
      currentPage: state => parseInt(state.modulesData.filter.page, 10),
      perPage: state => parseInt(state.modulesData.filter.limit, 10),
      transitions: state => state.scenarios.common.transitions,
    }),
    displayModeToSwitch() {
      return this.type === 'tile'
        ? 'listView'
        : 'gridView';
    },
    queryFilter() {
      const query = _.pick(
        this.$route.query,
        [
          'limit',
          'sortBy',
        ],
      );

      return query || {};
    },
    queryModuleId () {
      const query = _.pick(
        this.$route.query,
        [
          'moduleId',
        ],
      );

      return query || {};
    },
    pagesCount() {
      return Math.ceil(this.count / this.actualFilter.limit);
    },
    pageFilter() {
      return Object.assign(
        {},
        this.defaultFilter,
        this.filter,
        this.queryFilter,
        this.searchQuery(),
      );
    },
    tooltipText() {
      return this.type === 'tile'
        ? this.$t('modules-page.switch-to-list-view')
        : this.$t('modules-page.switch-to-grid-view');
    },
  },
  watch: {
    queryFilter: {
      deep: true,
      handler(filter, oldFilter) {
        if (_.isEqual(filter, oldFilter)) {
          return;
        }

        let filterQuery = this.filter;
        if (
          filter.sortBy !== oldFilter.sortBy
          || filter.limit !== oldFilter.limit
        ) {
          filterQuery = { ...filterQuery, ...{ page: 1 } };
        }

        filterQuery = { ...filterQuery, ...this.searchQuery() };

        this.$store.commit(
          'modulesData/SET_FILTER',
          Object.assign(
            {},
            this.actualFilter,
            Object.assign(
              {},
              this.defaultFilter,
              filterQuery, filter),
          ),
        );
      },
    },
    queryModuleId: {
      deep: true,
      handler (moduleId, oldModuleId) {
        if (_.isEqual(moduleId, oldModuleId)) {
          return;
        }

        this.openModuleDetailsPopup(moduleId.moduleId);
      },
    },
  },
  created() {
    if (this.showDisplayModeSwitcher && this.$cookies.get('display_mode')) {
      this.type = this.$cookies.get('display_mode');
    }
  },
  beforeMount() {
    this.$store.commit(
      'modulesData/SET_FILTER',
      this.pageFilter,
    );

    if (this.queryModuleId) {
      this.openModuleDetailsPopup(this.queryModuleId.moduleId);
    }
  },
  methods: {
    changePageHandler(page) {
      this.$store.commit(
        'modulesData/SET_FILTER',
        Object.assign(
          {},
          this.defaultFilter,
          this.filter,
          this.queryFilter,
          { page },
          this.searchQuery(),
        ),
      );
    },
    switchDisplayModeHandler() {
      this.type = (this.type === 'tile')
        ? 'row'
        : 'tile';

      this.$cookies.set('display_mode', this.type);
    },
    searchQuery() {
      return _.pick(this.$route.query, ['search']) || {};
    },
    openModuleDetailsPopup (moduleId) {
      if (!moduleId) {
        return;
      }

      const stateToSet = Object.keys(this.transitions).length && this.transitions[moduleId]
        ? this.transitions[moduleId].stateToSet
        : '';

      this.$store.commit('singleModule/SET_ID', moduleId);
      this.$store.dispatch('singleModule/fetchModuleData', { stateToSet });
    },
  },
};
</script>

<style lang="scss" scoped>
@import '../../stylesheets/common';

.modules-page {
  .modules-list {
    &.tile {
      @include vr($margin-bottom: 1.5)
    }

    &.row {
      @include vr($margin-bottom: 2)
    }
  }
}

.display-mode-switcher {
  margin-left: $rhythmic-unit;
  cursor: pointer;
  @include vr($line-height: 1);

  &:hover {
    color: $icon-hover;
  }
}
</style>
