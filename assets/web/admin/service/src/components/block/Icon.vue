<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <svg
    v-if="sprite"
    :data-inline="inline"
    :viewBox="sprite.viewBox"
    :style="styles"
    :data-icon="name"
    class="icon">
    <use :xlink:href="'#' + sprite.id"/>
  </svg>
</template>

<script>


const spritesheet = {
  /* eslint-disable global-require */
  logo: require('../../assets/logo.svg').default,
  purpleLogo: require('../../assets/purple-logo.svg').default,
  logoRect: require('../../assets/logo-rect.svg').default,
  downloads: require('../../assets/downloads.svg?fill=currentColor').default,
  leftChevron: require('../../assets/left-chevron.svg?fill=currentColor').default,
  rightChevron: require('../../assets/right-chevron.svg?fill=currentColor').default,
  account: require('../../assets/account.svg').default,
  cross: require('../../assets/cross.svg?fill=currentColor').default,
  locked: require('../../assets/lock.svg').default,
  unlocked: require('../../assets/lock-open.svg').default,
  external: require('../../assets/external.svg?fill=currentColor').default,
  noResult: require('../../assets/no-result.svg').default,
  noUpgrades: require('../../assets/no-upgrades.svg').default,
  archive: require('../../assets/archive.svg').default,
  trash: require('../../assets/trash.svg?fill=currentColor').default,
  settings: require('../../assets/settings.svg?fill=currentColor').default,
  rebuildStepInProgress: require('../../assets/rebuild-step-in-progress.svg').default,
  rebuildStepDone: require('../../assets/rebuild-step-done.svg').default,
  upgradeNoteWarning: require('../../assets/upgrade-note-warrning.svg').default,
  warning: require('../../assets/warning.svg').default,
  successUpgrade: require('../../assets/success-upgrade.svg').default,
  selfUpgrade: require('../../assets/self-upgrade.svg').default,
  emptyPurchases: require('../../assets/empty-purchases.svg').default,
  check: require('../../assets/check.svg').default,
  gridView: require('../../assets/grid-view.svg').default,
  listView: require('../../assets/list-view.svg').default,
  SL: require('../../assets/SL.svg').default,
  skinSwitchingProgress: require('../../assets/skin-switching-progress.svg').default,
  skinSwitchingSuccess: require('../../assets/skin-switching-success.svg').default,
  rebuildError: require('../../assets/rebuild-error.svg').default,
  burgerMenu: require('../../assets/burger-menu.svg').default,
  appStore: require('../../assets/app-store.svg').default,
  themeStore: require('../../assets/theme-store.svg').default,
  myApps: require('../../assets/my-apps.svg').default,
  purchases: require('../../assets/purchases.svg').default,
  updates: require('../../assets/updates.svg').default,
  selected: require('../../assets/selected.svg').default,
  subscriptions: require('../../assets/subscriptions.svg').default,
  search: require('../../assets/search.svg').default,
  minicart: require('../../assets/minicart.svg').default,
  /* eslint-disable global-require */
};

export default {
  name: 'Icon',
  props: {
    name: {
      type: String,
      default: '',
    },
    width: {
      type: String,
      default: '',
    },
    height: {
      type: String,
      default: '',
    },
    inline: {
      type: Boolean,
      default: true,
    },
    colorable: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    styles() {
      const obj = {};

      if (this.width) {
        obj.width = this.width;
      }

      if (this.height) {
        obj.height = this.height;
      }

      return obj;
    },
    sprite() {
      return spritesheet[this.name];
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

svg:not(:root).svg-inline--fa {
  height: $rhythmic-unit;
  vertical-align: inherit;
}

svg:not(:root).icon {
  &[data-inline] {
    height: $rhythmic-unit;
    width: $rhythmic-unit;
  }
}
</style>
