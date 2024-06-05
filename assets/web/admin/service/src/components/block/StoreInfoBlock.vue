<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="store-info">
    <p>
      <strong>
        <span class="store-info__name"><i18n path="tech-info.store-version" /></span>
        <span class="store-info__value">{{ storeInfo.version }}</span>
      </strong>
    </p>
    <p>
      <span class="store-info__name"><i18n path="tech-info.license" /></span>
      <span class="store-info__value">{{ storeInfo.license }}</span>
    </p>
    <p>
      <span class="store-info__name"><i18n path="tech-info.install-date" /></span>
      <span class="store-info__value">
        {{
          storeInfo.install_timestamp
            ? this.$d(new Date(storeInfo.install_timestamp * 1000), "long")
            : ""
        }}
      </span>
    </p>
    <p>
      <span class="store-info__name"><i18n path="tech-info.active-template" /></span>
      <span class="store-info__value">{{ storeInfo.skin }}</span>
    </p>
  </div>
</template>

<script>
export default {
  name: 'StoreInfoBlock',
  components: {},
  mixins: [],
  props: {
    storeInfo: {
      type: Object,
      default: () => {},
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.store-info {
  p {
    margin-top: $rhythmic-unit / 4;
    margin-bottom: 0;
  }
  &__name:after {
    content: ": ";
  }
}
</style>
