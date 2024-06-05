<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div class="minicart">
    <div
      :class="`icon minicart__icon${hasData ? ' active' : ''}`"
      @click="toggleClick">
      <icon name="minicart"/>
    </div>
    <div
      v-show="miniCartDisplay"
      class="dropdown-menu minicart__dropdown">
      <div
        v-if="hasData">
        <scenario-section
          :transitions="transitions"
          :discard="discard"
          :open="miniCartDisplay"/>
      </div>
      <div
        v-else
        class="minicart__dropdown--empty">
        <i18n path="No modules selected"/>
        <a :href="getMyAppsUrl">
          <i18n path="navigation.my_addons"/>
        </a>.
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import ScenarioSection from './ScenarioSection';
import Icon from '../block/Icon';
import appConfig from '../../../config/app-config';

export default {
  name: 'Scenario',
  components: {
    Icon,
    ScenarioSection,
  },
  props: {
    type: {
      type: String,
      default() {
        return 'common';
      },
    },
  },
  computed: {
    ...mapState({
      transitions(state) {
        return state.scenarios[this.type].transitions;
      },
      alreadyStartedScenarioId(state) {
        return state.scenarios[this.type].alreadyStartedScenarioId;
      },
      xcart(state) {
        return state.xcart;
      },
      miniCartDisplay(state) {
        return state.scenarios[this.type].miniCartDisplay;
      },
      miniCartDisplayHasNeverBeenShown(state) {
        return state.scenarios[this.type].miniCartDisplayHasNeverBeenShown;
      },
    }),
    getMyAppsUrl() {
      return `${appConfig.url}/${appConfig.adminScript}/?target=apps`;
    },
    hasData() {
      return this.transitions && Object.keys(this.transitions).length > 0;
    },
  },
  watch: {
    alreadyStartedScenarioId(scenarioId) {
      if (scenarioId !== null) {
        const publicDir = appConfig.publicDir !== '' ? `/${appConfig.publicDir}` : '';
        const encodedCurrentUrl = encodeURIComponent(window.location.href);
        const rebuildUrl =
          `${appConfig.url + publicDir}/rebuild.html?scenarioId=${scenarioId}&returnURL=${encodedCurrentUrl}`;

        const errorMessage = this.$t('Hold on a moment, please. Redeploy is in progress')
          .replace(
            '$rebuildLink',
            rebuildUrl,
          );

        this.xcart.trigger(
          'message',
          {
            type: 'error',
            message: errorMessage,
          },
        );
      }
    },
    transitions(transitions) {
      if (Object.keys(transitions).length === 1 && this.miniCartDisplayHasNeverBeenShown) {
        this.$store.commit('scenarios/common/SET_MINI_CART_DISPLAY', true);
      }
    },
  },
  created() {
    this.$store.commit('scenarios/common/SET_MINI_CART_DISPLAY', this.hasData);
  },
  methods: {
    discard(e) {
      e.preventDefault();
      this.$store.dispatch(`scenarios/${this.type}/discard`);
    },
    toggleClick() {
      this.$store.commit('scenarios/common/SWITCH_MINI_CART_DISPLAY');
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.minicart {
  position: relative;

  &__icon {
    width: $admin-minicart-icon-width;
    height: $admin-minicart-icon-height;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    svg.icon[data-inline] {
      width: 100%;
      height: 100%;
    }

    &.active {
      &:before {
        display: block;
        content: '';
        position: absolute;
        background: $app-header-background;
        top: 10px;
        right: 8px;
        width: 6px;
        height: 6px;
      }

      &:after {
        display: block;
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        border: 3px solid $app-header-background;
        background: $color-orange;
        width: 12px;
        height: 12px;
        border-radius: 50%;
      }
    }
  }

  &__dropdown {
    .minicart & {
      display: block;
      width: 460px;
      max-height: 500px;
      border-radius: $border-radius;
      padding: $admin-minicart-dropdown-padding;
      font-size: $admin-minicart-font-size;
      line-height: $line-height-small;
      border: 0;
      top: calc(100% + (0.25 * #{$rhythmic-unit}));
      left: auto;
      right: -(0.25 * $rhythmic-unit);
      overflow: hidden;
      margin-top: 0;
    }

    &--content {
      margin-bottom: 2.5 * $rhythmic-unit;

      .title {
        font-size: $admin-minicart-title-font-size;
        font-weight: 600;
        margin-bottom: $rhythmic-unit;
      }

      &--list {
        max-height: calc(500px - (6 * #{$rhythmic-unit}));
        overflow: auto;

        width: 100%;
        @include scrollbars();

        &.has-shadow {
          width: calc(100% + 17px);
          padding-right: 12px;
          padding-bottom: 0.5 * $rhythmic-unit;
        }
      }

      ol {
        list-style: decimal;
        margin-left: 1.25 * $rhythmic-unit;

        li {
          font-size: $admin-minicart-font-size;
          line-height: $line-height-small;
          padding-left: 0.25 * $rhythmic-unit;

          & + li {
            margin-top: 0.5 * $rhythmic-unit;
          }
        }
      }
    }

    .transition {
      display: flex;
      justify-content: space-between;
    }

    .transition-type {
      font-weight: 600;
    }
  }
}

.run-scenario-wrapper {
  position: absolute;
  bottom: 0;
  left: 0;
  padding: (0.5 * $rhythmic-unit) $rhythmic-unit;
  width: 100%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;

  &.has-shadow {
    box-shadow: 0 -5px 10px 0 rgba(51, 51, 51, 0.1);
  }

  .run-rebuild {
    width: 100%;
  }

  .discard {
    font-size: $admin-minicart-font-size;
    font-weight: normal;
  }
}
</style>
