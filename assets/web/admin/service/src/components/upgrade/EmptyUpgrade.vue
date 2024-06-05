<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="empty-upgrades-list">
    <icon
      name="noUpgrades"
      height="200px"
      width="320px"/>
    <i18n
      path="empty-upgrade.info"
      tag="h2"/>
  </div>
</template>

<script>
export default {
  name: 'EmptyUpgrade',
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.empty-upgrades-list {
  opacity: 1;
  transition: $basic-transition;
  display: flex;
  height: 100%;
  width: 100%;
  flex-grow: 1;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;

  h2 + p {
    margin-top: 0;
  }
}
</style>
