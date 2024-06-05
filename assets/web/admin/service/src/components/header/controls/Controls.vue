<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="modules-list-controls">
    <slot/>
  </div>
</template>

<script>
import RoutingHelpers from '../../mixins/RoutingHelpers';

export default {
  name: 'Controls',
  mixins: [RoutingHelpers],
};
</script>


<style lang="scss">
  @import '../../../stylesheets/common';

  .modules-list-controls {
    margin-left: auto;
    @include vr($height: 2);
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
    position: relative;
    z-index: 10;

    .filters {
      display: flex;
    }

    .modules-list-filters {
      display: flex;
      align-items: center;

      & > * + * {
        margin-left: $margin-base;
      }

      .dropdown-options ul {
        column-gap: $margin-base * .5;
      }
    }

    button + button {
      margin-left: $rhythmic-unit;
    }

    .modules-list-sort-by {
      margin-left: $margin-base;

      .label {
        margin-right: $font-size-base * .3;
        color: $tips;
      }
    }

    .actions {
      display: flex;
      margin-left: $margin-base;
      white-space: nowrap;

      button + button {
        @include vr($margin-left: 1);
      }
    }
  }
</style>
