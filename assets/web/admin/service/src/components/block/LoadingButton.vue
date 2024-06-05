<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <button
    :class="newStyleClass"
    @click.prevent.stop="clickHandler">
    <div
      v-if="isLoading"
      class="loader">
      <div class="dot dot1"/>
      <div class="dot dot2"/>
      <div class="dot dot3"/>
    </div>
    <div class="caption">
      <i18n
        :path="path"
        tag="span"/>
    </div>
  </button>
</template>

<script>
export default {
  name: 'LoadingButton',
  props: {
    styleClass: {
      type: String,
      default: '',
    },
    path: {
      type: String,
      default: '',
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    newStyleClass() {
      return `loading-button ${this.styleClass}`;
    },
  },
  methods: {
    clickHandler() {
      this.$emit('clickHandler');
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.loading-button {
  .loader {
    .dot {
      display: inline-block;
      width: 6px;
      height: 6px;
      border-radius: 100%;
      background-color: $link;
      vertical-align: middle;

      &.dot1 {
        animation: opacitychange 1s ease-in-out infinite;
      }

      &.dot2 {
        animation: opacitychange 1s ease-in-out 0.33s infinite;
      }

      &.dot3 {
        animation: opacitychange 1s ease-in-out 0.66s infinite;
      }
    }

    & + .caption {
      visibility: hidden;
      height: 0;
    }
  }

  &.primary .loader .dot {
    background-color: #fff;
  }
}

@keyframes opacitychange {
  0%, 100% {
    opacity: 0;
  }

  60% {
    opacity: 1;
  }
}
</style>
