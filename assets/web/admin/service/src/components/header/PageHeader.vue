<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="page-header">
    <h1>{{ $t(title) }}</h1>
  </div>
</template>

<script>
export default {
  name: 'PageHeader',
  props: {
    title: {
      type: String,
      default: '',
    },
  },
  computed: {
  },
};
</script>

<style lang="scss" scoped>
  .app-page-container {
    .page-header {
      margin-top: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-bottom: 0;
      border-bottom: 0;
    }
  }
</style>
