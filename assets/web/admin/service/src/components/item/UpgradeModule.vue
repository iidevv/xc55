<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownTarget, CheckEmptyScriptTag -->
<template>
  <div
    :class="classes"
    class="module tile upgrade">
    <div class="module-wrapper">

      <div>
        <div
          v-if="module.skinPreview"
          class="skin-preview">
          <img
            :src="module.skinPreview"
            :alt="module.name">
        </div>
        <div
          v-else
          class="image">
          <img
            v-if="module.listIcon"
            :alt="module.name"
            :src="module.listIcon"
            class="list-icon">
          <img
            v-else-if="module.icon"
            :src="module.icon"
            :alt="module.name">
          <img
            v-else
            :alt="module.name"
            src="../../stylesheets/addon_default.png">
        </div>
      </div>

      <div class="main">
        <div class="name">{{ module.readableName }}</div>
        <i18n
          path="module.upgrade.author"
          class="author">
          {{ module.readableAuthor }}
        </i18n>
      </div>

      <div class="info">
        <div class="params">
          <i18n
            v-if="module.enabled || module.id === 'CDev-Core'"
            path="Enabled"
            class="availability state enabled"/>
          <i18n
            v-else-if="!module.enabled"
            path="Disabled"
            class="availability state disabled"/>
          <span
            class="version"
            v-html="getVersion(module)"/>

          <i18n
            path="module.changelog"
            tag="a"
            href="#"
            @click.stop.prevent="showChangelog"/>
        </div>
      </div>

      <div
        v-if="firstWarning"
        class="reveal-wrapper"
        tabindex="-1">
        <div class="reveal-collapsed">
          <module-alerts
            :messages="[firstWarning]"
            :module="module"/>
          <div
            v-if="module.skinPreview"
            class="skin-preview">
            <img
              :src="module.skinPreview"
              :alt="module.name">
          </div>
        </div>
      </div>

      <upgrade-module-actions
        :selected="selectedForUpgrade"
        :module="module"
        :is-tile="true"/>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import Tile from './Tile';
import UpgradeModuleActions from './action/UpgradeModuleActions';

export default {
  name: 'UpgradeModule',
  components: { UpgradeModuleActions },
  extends: Tile,
  computed: {
    ...mapState({
      selectedForUpgrade(state) {
        return state.upgrades.unselected.length === 0
          || !_.includes(state.upgrades.unselected, this.module.id);
      },
    }),
    firstWarning() {
      const message = this.module.messages && this.module.messages.length > 0
        ? this.module.messages[0]
        : null;

      return message && message.type !== 'success' ? message : null;
    },
  },
  methods: {
    showChangelog() {
      this.$modal.show('module-changelog-dialog', {
        title: 'changelog-dialog.module.title',
        entry: this.module,
      });
    },
    getVersion(module) {
      return Object.keys(module.version).length
        ? `${module.version.major}.${module.version.minor}.${module.version.build}`
        : '';
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.module.tile.upgrade .module-wrapper {
  .author {
    @include vr($margin-top: .5, $font-size: -1);
    color: $border;
  }

  .info .params {
    .availability {
      margin-left: 0;
    }

    .version {
      flex-grow: 1;
    }

    &.remove {
      font-weight: $font-weight-bold;
      color: $error_tips;
      margin-top: 0;

      span {
        @include vr($margin-top: .5);
      }
    }
  }

  .reveal-wrapper {
    height: 0;
  }
}

.module.tile.upgrade.expanded .module-wrapper .reveal-wrapper,
.module.tile.upgrade .module-wrapper:hover .reveal-wrapper {
  height: 0;
}

</style>
