<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <a
    :target="target"
    :href="href"
    :class="{ padded: padded }"
    class="external-link">
    <span>
      <slot/>
    </span>
    <icon
      name="external"
      width="8px"
      height="8px"/>
  </a>
</template>

<script>
export default {
  name: 'ExternalLink',
  props: {
    href: {
      type: String,
      default: '',
    },
    padded: {
      type: Boolean,
      default: true,
    },
    target: {
      type: String,
      default: '_blank',
    },
  },
};
</script>

<style lang="scss">
    @import '../../stylesheets/common';

    a.external-link {
        position: relative;
        display: inline-block;
        svg.icon {
            position: absolute;
            right: 0;
            top: 0;
            transform: translate(125%, 0);
            opacity: 0;
            transition: $basic-transition;
        }

        &.padded {
            padding-right: 15px;

            svg.icon {
                right: 12px;
                top: 8px;
            }
        }

        &:hover {
            svg.icon {
                opacity: 1;
            }
        }
    }
</style>
