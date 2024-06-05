<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="modules-list">
    <h2 class="modules-list__title">
      <i18n :path="title" />
    </h2>
    <div class="modules-list__query">
      <label>
        <span class="modules-list__query-label">
          <span><i18n path="tech-info.search-filter" /></span>
        </span>
        <span class="modules-list__query-field-holder">
          <input
            :value="query"
            placeholder="Filter"
            type="search"
            name="s"
            class="form-control modules-list__query-field"
            @input="setQuery"
          >
          <span class="modules-list__query-placeholder">
            <i18n path="tech-info.search-query" />
          </span>
        </span>
      </label>
    </div>
    <div class="modules-list__list-wrapper">
      <table class="modules-list__list">
        <thead>
          <tr>
            <th
              v-for="(column, i) in columns"
              :key="`column-${i}`"
              :class="getHeaderCellClasses(i)"
            >
              <button
                class="modules-list__list-th-btn"
                @click="() => { handleSortButtonClick(i); }"
              >
                <span class="modules-list__list-th-text">
                  <i18n :path="column.title" />
                </span>
              </button>
            </th>
          </tr>
        </thead>
        <tbody
          v-if="!rows.length"
        >
          <tr>
            <td
              :colspan="columns.length"
              class="modules-list__list-no-results"
            >
              <i18n :path="listEmptyLabel" />
            </td>
          </tr>
        </tbody>
        <tbody
          v-else-if="!filteredRows.length"
        >
          <tr>
            <td
              :colspan="columns.length"
              class="modules-list__list-no-search-results"
            >
              <i18n :path="noSearchResultsLabel" />
            </td>
          </tr>
        </tbody>
        <tbody
          v-else
        >
          <tr
            v-for="(row, i) in filteredRows"
            :key="`row-${i}`"
          >
            <td
              v-for="(column, j) in columns"
              :key="`cell-${i}-${j}`"
            >
              {{ renderCellContent(filteredRows, i, j) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TechInfoModulesPage',
  props: {
    title: {
      type: String,
      default: '',
    },
    rows: {
      type: Array,
      default: () => [],
    },
    columns: {
      type: Array,
      default: () => [],
    },
    listEmptyLabel: {
      type: String,
      default: '',
    },
    noSearchResultsLabel: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      filteredRows: [],
    };
  },
  created() {
    this.filteredRows = this.rows;
    this.query = '';
    this.sortOrder = true;
    this.sortBy = -1;
  },
  beforeMount() {
    this.filter();
    this.sort();
  },
  methods: {
    update() {
      this.filter();
      this.sort();
    },
    filter() {
      if (this.query) {
        const query = this.query.toLowerCase();
        this.filteredRows = this.rows.filter((row, i) => {
          for (let j = 0; j < this.columns.length; j += 1) {
            const cellContent = this.renderCellContent(this.rows, i, j);
            if (cellContent.toLowerCase().indexOf(query) !== -1) {
              return true;
            }
          }
          return false;
        });
      } else {
        this.filteredRows = this.rows;
      }
    },
    sort() {
      if (this.sortBy >= 0 && this.rows.length) {
        const column = Math.min(this.sortBy, this.columns.length - 1);
        const field = this.getColumnField(column);
        const type = this.getColumnType(column);
        this.filteredRows.sort((m1, m2) => {
          let v1 = m1[field];
          let v2 = m2[field];
          if (type === 'string') {
            v1 = v1.toString().toLowerCase();
            v2 = v2.toString().toLowerCase();
          }
          if (v1 < v2) {
            return this.sortOrder ? -1 : 1;
          } else if (v1 > v2) {
            return this.sortOrder ? 1 : -1;
          }
          return 0;
        });
      }
    },
    getColumnType(column) {
      return (this.columns[column] && this.columns[column].type)
        ? this.columns[column].type
        : 'string';
    },
    getColumnField(column) {
      return (this.columns[column] && this.columns[column].field)
        ? this.columns[column].field
        : '';
    },
    changeSortOrder() {
      this.sortOrder = !this.sortOrder;
      this.update();
    },
    setSortColumn(column) {
      const newColumn = (
        this.rows.length
          ? Math.min(Math.max(0, column), this.columns.length - 1)
          : -1
      );
      if (newColumn !== this.sortBy) {
        this.sortBy = newColumn;
        this.sortOrder = true;
        this.update();
      }
    },
    setQuery(event) {
      this.query = event.target.value;
      this.update();
    },
    getHeaderCellClasses(column) {
      return {
        'modules-list__list-th-title': true,
        'modules-list__list-th-title--sort-asc': this.sortBy === column && this.sortOrder,
        'modules-list__list-th-title--sort-desc': this.sortBy === column && !this.sortOrder,
      };
    },
    renderCellContent(rows, i, j) {
      const columnType = this.getColumnType(j);
      const content = rows[i][this.getColumnField(j)];
      switch (columnType) {
        case 'date': return this.$d(new Date(content * 1000), 'short');
        default: return content.toString();
      }
    },
    handleSortButtonClick(column) {
      if (column === this.sortBy) {
        this.changeSortOrder();
      } else {
        this.setSortColumn(column);
      }
      this.update();
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

$sorter-icon-width: $rhythmic-unit / 5;
$sorder-icon-padding: $rhythmic-unit - $sorter-icon-width;

.modules-list {
  margin-top: 2 * $rhythmic-unit;
  &__query {
    margin: $rhythmic-unit 0 0 0;
    &-label {
      margin-right: ($rhythmic-unit / 2);
      &:after {
        content: ":";
      }
    }
    label {
      display: inline-flex;
      align-items: center;
    }
    &-field {
      &-holder {
        position: relative;
      }
      &::placeholder {
        color: transparent;
        font-size: 0;
        opacity: 0;
      }
      &:placeholder-shown,
      &:-moz-placeholder {
        ~ .modules-list__query-placeholder {
          display: block;
        }
      }
    }
    &-placeholder {
      position: absolute;
      top: 50%;
      transform: translate3d(0, -50%, 0);
      left: 11px;
      opacity: .5;
      pointer-events: none;
      display: none;
    }
  }
  &__list {
    font-size: $table-font-size;
    line-height: $table-line-height;
    width: 100%;
    &-wrapper {
      margin: $rhythmic-unit 0 0 0;
      border: $table-border;
      border-radius: $table-border-radius;
      overflow: hidden;
    }
    &-th {
      &-btn {
        appearance: none;
        -webkit-appearance: none;
        background: transparent;
        border: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
        font-weight: 600;
      }
      &-text {
        position: relative;
        padding-right: $sorter-icon-width + $sorder-icon-padding;
        &:before, &:after {
          border: $sorter-icon-width solid transparent;
          content: "";
          display: block;
          height: 0;
          right: 0;
          top: 50%;
          position: absolute;
          width: 0;
          margin-top: -2 * $sorter-icon-width - 1px;
        }
        &:before {
          border-bottom-color: currentColor;
          margin-top: -2 * $sorter-icon-width;
        }
        &:after {
          border-top-color: currentColor;
          margin-top: (-2 * $sorter-icon-width) + ($rhythmic-unit / 2);
        }
      }
      &-title {
        &--sort-asc .modules-list__list-th-text:after,
        &--sort-desc .modules-list__list-th-text:before {
          display: none;
        }
      }
    }
    th {
      background: $table-thead-bg;
    }
    td {
      border-top: $table-border;
    }
    th, td {
      padding: $table-cell-padding;
    }
    &-no-results, &-no-search-results {
      text-align: center;
    }
  }
  .modules-list__title {
    margin: 0;
  }
}
</style>
