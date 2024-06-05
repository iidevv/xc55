<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div
    :class="classes"
    class="alert-wrapper">
    <slot :params="params"/>
  </div>
</template>

<script>
export default {
  name: 'BlockAlert',
  props: {
    classes: {
      type: String,
      default: '',
    },
    message: {
      type: String,
      default: '',
    },
    params: {
      type: Object,
      default: () => {},
    },
  },
  methods: {
    formatParams(params) {
      return params;
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.alert-wrapper {
  &.danger {
    background-color: $error_fill;
  }

  &.warning {
    background-color: $warning_fill;
  }

  &.info {
    background-color: $info_fill;
  }

  &.success {
    background-color: $success-fill;
  }

  &.alert-dismissible {
    padding-right: 48px;

    button {
      position: absolute;
      border: none;
      box-shadow: none;
      background: none;
      font-size: 27px;
      padding: 0;
      height: 12px;
      line-height: 12px;
      width: 12px;
      top: 12px;
      right: 18px;
      color: $title-text;
    }
  }
}
</style>
