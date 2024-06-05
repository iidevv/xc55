<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <div>
    <div
      v-if="hasMessage">
      <block-alert
        v-for="(message, idx) in messages"
        :key="idx"
        :classes="`module-alert ${message.type}`">
        <div
          v-if="message.message === 'module_state_message.update_available'">
          <i18n path="module_state_message.update_available">
            <span
              class="version"
              v-html="getMessageParams(message).version"/>
            <i18n
              path="module_state_message.update_available.update_link"
              tag="a"
              href="#"
              @click.prevent.stop="goToUpgrade(getMessageParams(message).type)"/>
          </i18n>
        </div>
      </block-alert>
    </div>
    <div
      v-if="hasWarning"
      :path="warning.message"
      class="module-alert">
      <div class="message">
        <i18n :path="warning.message">
          <ul class="module-names">
            <router-link
              v-for="module in warning.params"
              :key="module.id"
              :to="{query: moduleQuery(module.id)}"
              tag="li"
              @click.native="clickHandler">
              <i18n path="module_alert.moduleName">
                <span>{{ module.moduleName }}</span>
                <span>{{ module.authorName }}</span>
              </i18n>
            </router-link>
          </ul>
        </i18n>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import BlockAlert from '../block/BlockAlert';

export default {
  name: 'ModuleAlert',
  components: {
    BlockAlert,
  },
  props: {
    messages: {
      type: Array,
      default: () => [],
    },
    warning: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    ...mapState({
      transitions: state => state.scenarios.common.transitions,
    }),
    hasWarning() {
      return !_.isEmpty(this.warning);
    },
    hasMessage() {
      return !_.isEmpty(this.messages);
    },
  },
  methods: {
    moduleQuery(moduleId) {
      const query = JSON.parse(JSON.stringify(this.$route.query || {}));
      return Object.assign(query, { moduleId });
    },
    clickHandler() {
      const query = JSON.parse(JSON.stringify(this.$route.query || {}));
      const moduleId = query.moduleId || this.warning.params[0].id;

      if (moduleId) {
        const stateToSet = Object.keys(this.transitions).length && this.transitions[moduleId]
          ? this.transitions[moduleId].stateToSet
          : '';

        this.$store.commit('singleModule/SET_ID', moduleId);
        this.$store.dispatch(
          'singleModule/fetchModuleData',
          { stateToSet },
        );
      }
    },
    getUpgradeLink(type) {
      return `/upgrade/${type}`;
    },
    goToUpgrade(type) {
      this.$router.push({ path: this.getUpgradeLink(type) });
      this.hideDialog();
    },
    getMessageParams(message) {
      return message.params || {};
    },
    hideDialog() {
      this.$modal.hide('module-details');
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.module-alert {
  @include vr($padding: .5);
  border-radius: $border-radius;
  position: relative;
  background-color: $warning_fill;
  cursor: initial;
  margin-bottom: .5 * $rhythmic-unit;

  &.try-switch {
    animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
  }

  .message {
    overflow: hidden;
    text-overflow: ellipsis;
    .module-names {
      li {
        color: $link;
        cursor: pointer;

        &:hover {
          text-decoration: underline;
        }
      }
    }
  }
}

@keyframes shake {
  10%, 90% {
    transform: translate3d(-1px, 0, 0);
  }

  20%, 80% {
    transform: translate3d(2px, 0, 0);
  }

  30%, 50%, 70% {
    transform: translate3d(-4px, 0, 0);
  }

  40%, 60% {
    transform: translate3d(4px, 0, 0);
  }
}
</style>
