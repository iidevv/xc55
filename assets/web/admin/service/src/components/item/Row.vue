<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div
    :class="classes"
    :id="module.id"
    class="module installed-module">
    <div class="image">
      <!-- TODO: https://sellerlabs.atlassian.net/browse/ECOM-1345 -->
      <a-external
        v-if="module.pageUrl"
        :href="module.pageUrl"
        :padded="false">
        <img
          :src="module.icon"
          :alt="module.name">
      </a-external>
      <div v-else>
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
            v-if="module.icon"
            :src="module.icon"
            :alt="module.name">
          <img
            v-else
            :alt="module.name"
            src="../../stylesheets/addon_default.png">
        </div>
      </div>
    </div>
    <div class="main">
      <div class="name">
        <a-external
          v-if="module.pageUrl"
          :href="module.pageUrl">
          {{ module.moduleName }}
        </a-external>
        <span v-else>{{ module.moduleName }}</span>
      </div>

      <div class="params">
        <span class="author">{{ module.authorName }}</span>
        <span
          class="version"
          v-html="module.version"/>
      </div>

      <slot name="additional-params"/>

      <div
        class="actions-wrapper">
        <installed-module-actions :module="module"/>
        <module-alert
          v-if="hasWarning"
          :warning="module.warning"/>
      </div>
    </div>
    <div
      v-if="isIframe"
      class="info">
      <p class="description">{{ module.description }}</p>
    </div>
  </div>
</template>

<script>
import appConfig from '../../../config/app-config';
import InstalledModuleActions from './action/InstalledModuleActions';
import ModuleAlert from './ModuleAlert';
import ModuleLink from './ModuleLink';
import { MODULE_STATES } from '../../../src/constants';

export default {
  components: {
    InstalledModuleActions,
    ModuleAlert,
    ModuleLink,
  },
  props: {
    module: {
      type: Object,
      default: () => {
      },
    },
  },
  data() {
    return {
      MODULE_STATES: Object.freeze(MODULE_STATES),
    };
  },
  computed: {
    hasWarning() {
      return !_.isEmpty(this.module.warning);
    },
    classes() {
      return {
        'state-remove': this.module.scenarioState === MODULE_STATES.REMOVED,
      };
    },
    isIframe() {
      return this.$route.matched.some(m => m.name === 'iframe');
    },
  },
  methods: {
    isCore() {
      return false;
    },
    formatDate(timestamp) {
      return this.$d(new Date(timestamp * 1000), 'short');
    },
    manageLayoutHandler() {
      window.location = `${appConfig.url}/${appConfig.adminScript}/?target=layout`;
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.module.installed-module {
  display: flex;
  justify-content: flex-start;
  align-items: flex-start;
  border-radius: $border-radius;
  @include vr(
    $margin: 0 1,
    $padding: .25
  );

  .tags {
    @include module-tags;

    .module-tag {
      @include vr($margin: 0 .2 .375);
    }
  }

  &.state-remove {
    border: solid 1px $remove-border-color;
    background-color: $remove-background-color;
  }

  & + & {
    @include vr($margin-top: 1);
  }

  .image {
    @include module-image;

    > a {
      > svg {
        display: none;
      }
    }

    &,
    img {
      @include vr($height: 3.5, $width: 3.5);

      .iframe-view & {
        @include vr($height: 4, $width: 4);
      }
    }
  }

  .main {
    margin-left: $line-height-base * 4 / 3;
    flex-shrink: 0;
    @include vr($width: 24);

    .iframe-view & {
      @include vr($width: 16);
    }

    .name {
      font-weight: $font-weight-bold;
    }

    .params {
      @include vr($font-size: -1);
      display: flex;
      color: $tips;

      * + *:before {
        content: '';
        box-shadow: 0 0 0 .5px $input-border-color;
        width: 1px;
        color: $border;
        @include vr($margin: 0 .5);
      }
    }

    .actions-wrapper {
      display: flex;
      align-items: center;
      @include vr($margin-top: .5);

      .onoffswitch {
        height: auto;
      }

      .module-alert {
        position: relative;
        padding: 0;
        @include vr($margin-top: .5, $margin-left: .5, $margin-right: .5);

        .message {
          display: none;
        }

        &:before {
          @include vr($height: 1);
          padding: 7px 16px;
          display: flex;
          align-items: center;
          justify-content: center;
          content: '!';
          font-weight: bold;
          background-color: $warning_fill;
          border-radius: $border-radius;
        }

        &:hover {
          .message {
            display: block;
            position: absolute;
            width: max-content;
            max-width: 232px;
            top: $line-height-base;
            z-index: 100;
            @include vr($padding: .25);
            margin-top: 2px;
            background-color: $warning_fill;
            border-radius: 2px;
            box-shadow: $dropdown-shadow;
          }
        }
      }
    }

    .installed-module.actions {
      @include vr($margin-top: .5);

      .actions + .module-alert {
        @include vr($margin-left: .5);
      }

      .module-state-switcher {
        &,
        .input-checkbox {
          display: flex;
        }
      }

      .action.settings {
        @include vr($margin-left: 1);
        order: 0;

        a {
          display: flex;

          svg {
            @include vr($margin-right: .5);
          }
        }
      }

      .action.download {
        @include vr($margin-left: 1);
        order: 3;
      }

      .action.remove {
        order: 4;
        display: flex;
        align-items: center;

        &:before {
          @include vr($margin: 0 .5);
          box-shadow: none;
          height: $rhythmic-unit;
          border-left: 1px solid $light-gray-color;
        }

        a {
          display: flex;
          align-items: center;
        }
      }
    }
  }

  .info {
    @include vr($margin-left: 1);
    flex: 1 1 auto;
    text-align: left;

    .description {
      margin-top: 0;
    }

    .additional-actions {
      @include vr($margin-bottom: 1);
    }
  }

  .state {
    font-weight: $font-weight-bold;
    color: $boring-green;

    &.disabled {
      color: $tips;
    }
  }

  .installed-module.actions {
    .upgrade-request {
      order: 1;

      button {
        @include vr($margin-left: 1);
      }
    }
  }
}
</style>
