<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="pagination-wrapper">
    <v-pagination
      v-model="changePage"
      :page-count="totalPages"
      :classes="bootstrapPaginationClasses"
      :labels="paginationAnchorTexts"/>
  </div>
</template>

<script>
export default {
  name: 'Pager',
  props: {
    currentPage: {
      type: Number,
      required: true,
    },
    totalPages: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      bootstrapPaginationClasses: {
        liActive: 'active',
      },
      paginationAnchorTexts: {
        first: '<i class="fa fa-angle-double-left"></i>',
        prev: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        last: '<i class="fa fa-angle-double-right"></i>',
      },
    };
  },
  computed: {
    changePage: {
      get() {
        return this.currentPage;
      },
      set(val) {
        const correctedValue = val > this.totalPages ? this.totalPages : val;
        const pageName = this.$router.currentRoute.path.substring(1);

        this.$emit('change', correctedValue);
        this.$cookies.set(`current_page_${pageName}`, correctedValue);
      },
    },
  },
};
</script>

<style lang="scss">
 @import '../../stylesheets/common';
 .pagination-wrapper {
   display: flex;

    ul {
      display: flex;
    }

   li {
     button {
       appearance: none;
       color: $link;
       background-color: #fff;
       border: 1px solid $light-gray-color;
       text-align: center;
       width: auto;
       min-width: $input-height-base;
       height: $input-height-base;
       margin-left: -1px;

       &:hover {
         background: $light_blue_color;
       }
     }

     &.active {
       button {
         background: $link;
         color: #fff;
         border-color: $link
       }
     }

     &:first-child {
       button {
         border-radius: $border-radius 0 0 $border-radius;
       }
     }

     &:last-child {
       button {
         border-radius: 0 $border-radius $border-radius 0;
       }
     }
   }
 }
</style>
