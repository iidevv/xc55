<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <a
    v-if="!isDemoMode"
    :href="settingsLink"
    :title="$t('Go to external App Store')"
    class="go-to-external-app-store"
    @click.stop>
    {{ $t('Go to external App Store') }}
    <icon
      name="external"
      width="10px"
      height="10px"
    />
  </a>
</template>

<script>
import { mapState } from 'vuex';
import Icon from '../block/Icon';
import appConfig from '../../../config/app-config';
import { parseModuleScenarioFromUrlHash } from '../../utils';

export default {
  name: 'GoToExternalAppStoreLink',
  components: {
    Icon,
  },
  data() {
    return {
      finishedScenarioId: null,
      errCode: null,
    };
  },
  computed: {
    ...mapState({
      xcart(state) {
        return state.xcart;
      },
    }),
    isDemoMode() {
      return appConfig.isDemoMode;
    },
    settingsLink() {
      return this.$store.getters['appstore/getStoreUrl'];
    },
  },
  created() {
    this.$store.dispatch('appstore/fetchStoreId');
    const { modulesToToggle, scenarioObj, errCodeObj } = parseModuleScenarioFromUrlHash();
    if (scenarioObj) {
      this.finishedScenarioId = scenarioObj ? scenarioObj.Id : null;
    }
    if (errCodeObj) {
      this.errCode = errCodeObj ? errCodeObj.Id : null;
    }
    function installModulesFromURL () {
      // eslint-disable-next-line no-unused-vars
      Object.entries(modulesToToggle).forEach((requestedModule) => {
        const moduleId = requestedModule[0];
        const requestedState = requestedModule[1];
        this.$store.commit('singleModule/SET_ID', moduleId);
        this.$store.dispatch('singleModule/fetchModuleData', { stateToSet: requestedState }).then(() => {
          const module = this.$store.getters['singleModule/getModule'];
          if (module && module.state !== requestedState) {
            this.$store.dispatch(
              'toggleModuleState',
              { module, stateToSet: requestedState },
            );
            this.$store.commit('scenarios/common/SET_MINI_CART_DISPLAY', true);
          }
          this.$store.commit('singleModule/SET_ID', null);
          this.$store.commit('singleModule/SET_MODULE', null);
        });
      });
    }

    installModulesFromURL.call(this);
  },
  updated() {
    if (this.finishedScenarioId) {
      this.xcart.trigger('message',
        {
          type: 'info',
          message: this.$t('scenario.transition.module.successful'),
        });
      this.finishedScenarioId = null;
    }
    if (this.errCode) {
      this.xcart.trigger('message',
        {
          type: 'error',
          message: this.$t(`scenario.transition.module.err.${this.errCode}`),
        });
      this.errCode = null;
    }
  },
};
</script>

<style lang="scss">
  @import '../../stylesheets/common';

  .go-to-external-app-store {
    margin-right: 25px;
    .icon {
      margin-left: 5px;
    }
  }
</style>
