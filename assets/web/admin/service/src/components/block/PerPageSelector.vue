<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="per-page-selector">
    <i18n path="Items per page"/>
    <select
      v-model="perPage"
      class="form-control"
      @change="onSelectChange">
      <option
        v-for="(item, index) in itemsPerPage"
        :key="index"
        :selected="perPage === item">
        {{ item }}
      </option>
    </select>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import RoutingHelpers from '../mixins/RoutingHelpers';
import { ITEMS_PER_PAGE } from '../../constants';

export default {
  name: 'PerPageSelector',
  mixins: [RoutingHelpers],
  props: {
    currentPage: {
      type: Number,
      default: 1,
    },
    currentPerPage: {
      type: Number,
      default: ITEMS_PER_PAGE[0],
    },
    count: {
      type: Number,
      default: 0,
    },
    itemsPerPage: {
      type: Array,
      default: ITEMS_PER_PAGE,
    },
  },
  data() {
    return {
      perPage: '',
    };
  },
  computed: {
    ...mapState({
      actualFilter: state => state.modulesData.filter,
    }),
  },
  created() {
    this.perPage = this.currentPerPage;
  },
  methods: {
    onSelectChange() {
      let newOffset = (this.currentPage - 1) * this.perPage;
      const newOffsetIsTooBig = (newOffset >= this.count);

      if (newOffsetIsTooBig) {
        const lastPage = Math.floor((this.count - 1) / this.perPage) + 1;
        newOffset = (lastPage - 1) * this.perPage;
      }

      const limit = this.perPage;

      this.queryUpdate({ limit });
    },
  },
};
</script>

<style lang="scss" scoped>
  @import '../../stylesheets/common';

  .pagination-wrapper {
    + .per-page-selector {
      margin-left: $rhythmic-unit;
    }
  }

  .per-page-selector {
    display: flex;
    align-items: center;

    span {
      @include vr($margin-right: 1);
      white-space: nowrap;
    }
  }
</style>
