<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress CheckEmptyScriptTag -->
<template>
  <div class="installed-module actions">
    <div
      v-if="module.type !== 'skin'"
      :class="{ disabled: !module.actions.switchState || isDemoMode}"
      class="action onoffswitch"
      @click.stop="toggleStateHandler">
      <input
        :checked="module.scenarioState === MODULE_STATES.ENABLED"
        type="checkbox">
      <label/>
    </div>

    <i18n
      v-if="module.actions.manageLayout"
      :href="manageLayoutLink"
      path="Manage layout"
      tag="a"
      class="action manage-layout"
      @click.stop/>

    <span
      v-if="module.actions.remove && !isTile && !isDemoMode"
      class="action remove">
      <a
        :title="$t('module_action.remove')"
        href="#"
        class="icon"
        @click.prevent="removeHandler">
        <icon
          name="trash"
          width="16px"/>
      </a>
    </span>

    <a
      v-if="(module.state === MODULE_STATES.ENABLED) && module.actions.showSettingsForm"
      :href="settingsLink"
      :title="$t('Settings')"
      class="action settings icon"
      @click.stop>
      <icon
        name="settings"
        width="16px"/>
      {{ $t('Settings') }}
    </a>
  </div>
</template>

<script>
import ModuleAlert from '../ModuleAlert';
import Icon from '../../block/Icon';
import appConfig from '../../../../config/app-config';
import { MODULE_STATES } from '../../../constants';

export default {
  name: 'InstalledModuleActions',
  components: {
    Icon,
    ModuleAlert,
  },
  props: {
    module: {
      type: Object,
      default: () => {
      },
    },
    isTile: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      MODULE_STATES: Object.freeze(MODULE_STATES),
    };
  },
  computed: {
    settingsLink() {
      return `${appConfig.url}/${appConfig.adminScript}/?target=module&moduleId=${this.module.author}-${this.module.name}`;
    },
    manageLayoutLink() {
      return `${appConfig.url}/${appConfig.adminScript}/?target=layout`;
    },
    hasWarning() {
      return !_.isEmpty(this.module.warning);
    },
    isDemoMode() {
      return appConfig.isDemoMode;
    },
  },
  methods: {
    toggleStateHandler() {
      if (this.module.actions.switchState && !this.isDemoMode) {
        const currentScenarioState = this.module.scenarioState;
        let stateToSet;

        if (
          currentScenarioState === MODULE_STATES.NOT_INSTALLED
          || currentScenarioState === MODULE_STATES.INSTALLED
        ) {
          stateToSet = MODULE_STATES.ENABLED;
        } else if (currentScenarioState === MODULE_STATES.ENABLED) {
          stateToSet = MODULE_STATES.INSTALLED;
        }

        if (stateToSet) {
          const rebuildState = this.$cookies.get('rebuild_success');

          if (rebuildState) {
            this.$cookies.remove('rebuild_success');
          }

          this.$store.dispatch(
            'toggleModuleState',
            {
              module: this.module,
              stateToSet,
            },
          );
        }
      }
    },
    removeHandler(event) {
      this.$store.dispatch(
        'removeModule',
        {
          module: this.module,
          event,
        },
      ).then(() => {
        this.$root.$emit('removeModuleAfter', this.module, event);
      });
      this.$emit('removeEvent', this.module, event);
    },
    manageLayoutHandler() {
      window.location = `${appConfig.url}/${appConfig.adminScript}/?target=layout`;
    },
  },
};
</script>

<style lang="scss" scoped>
@import '../../../stylesheets/common';

.installed-module.actions {
  z-index: 1;
  display: flex;
  align-items: center;
  height: $input-height-base;
  flex-wrap: wrap;

  .action {
    @include vr($margin-left: 1);

    &:first-child {
      margin-left: 0;
    }

    &.remove {
      &:before {
        font-size: $font-size-very-small;
        line-height: $line-height-small;
        content: '';
        box-shadow: 0 0 0 .5px $input-border-color;
        width: 1px;
        margin-left: 1rem;
        margin-right: 1rem;

        .modules-list.tile & {
          display: none;
        }
      }
    }

    &.settings {
      display: flex;
      align-items: center;

      > svg {
        @include vr($margin-right: .25);
      }
    }
  }

  .download-link:hover {
    cursor: pointer;
  }
}

.module.tile {
  &[class*="state-"] {
    .module-wrapper {
      .actions-wrapper {

        .installed-module {
          display: flex;
          height: 100%;

          .onoffswitch {
            margin-left: auto;
            margin-right: 0;
            order: 2;
          }
        }
      }
    }
  }
}
</style>
