<!--suppress HtmlUnknownTarget -->
<template>
  <div
    :class="classes"
    class="input-checkbox">
    <label
      v-if="isLockType"
      :for="id"
      class="ghost-tgl-btn">
      <icon
        :name="checked ? 'unlocked' : 'locked'"
        width="16px"
        height="16px"/>
      <input
        :id="id"
        :disabled="disabled"
        :checked="checked"
        class="tgl"
        type="checkbox"
        @click="handleClick">
    </label>
    <label
      v-else
      :for="id"
      class="ghost-tgl-btn">
      <input
        :checked="checked"
        :disabled="disabled"
        class="ghost-tgl"
        type="checkbox">
      <input
        :id="id"
        :disabled="disabled"
        :checked="checked"
        class="tgl tgl-square"
        type="checkbox"
        @click="handleClick">
      <label
        :for="id"
        class="tgl-btn">
        <span class="on-label">{{ $t(onLabel) }}</span>
        <span class="off-label">{{ $t(offLabel) }}</span>
      </label>
    </label>
    <label
      :for="id"
      class="text">
      <slot/>
    </label>
  </div>
</template>


<script>
export default {
  name: 'Checkbox',
  props: {
    type: {
      type: String,
      default: 'simple',
    },
    checked: {
      type: Boolean,
      default: false,
    },
    onLabel: {
      type: String,
      default: 'on',
    },
    offLabel: {
      type: String,
      default: 'off',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    classes() {
      return {
        [`type-${this.type}`]: true,
        disabled: this.disabled,
      };
    },
    isLockType() {
      return this.type === 'lock';
    },
    id() {
      return Math.random().toString(36).substr(2, 9);
    },
    label() {
      return this.checked ? this.onLabel : this.offLabel;
    },
  },
  methods: {
    handleClick(event) {
      event.preventDefault();
      setTimeout(() => {
        this.$emit('change', !this.checked);
      });
    },
  },
};
</script>


<style lang="scss">
@import '../../stylesheets/common';

.input-checkbox {
  @include vr($line-height: 1);
  display: inline-block;
  vertical-align: middle;
  transition: $basic-transition;

  .tgl {
    display: none;
  }

  .ghost-tgl {
    pointer-events: none;
  }

  .ghost-tgl-btn, .text {
    @include vr($line-height: 1);
    cursor: pointer;
    display: inline-block;
    vertical-align: top;
  }

  .ghost-tgl-btn > * {
    vertical-align: middle;
  }

  &:hover {
    color: $link;
  }
}

.input-checkbox.type-simple {
  .tgl-btn {
    .on-label, .off-label {
      display: none;
    }
  }
}

.input-checkbox.type-lock {
  position: relative;
  color: #708692;

  &:hover {
    color: $link;
  }

  &.disabled {
    display: none;
  }

  .ghost-tgl-btn {
    width: 30px;
    height: 30px;
    text-align: center;
    box-shadow: 0 0 0 1px transparentize(#708692, .5);
    border-radius: $border-radius;
    position: absolute;
    left: ($line-height-base * -2);
    top: 50%;
    transform: translateY(-50%);

    svg {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  }

  .text {
    margin-left: 0;
  }
}

.input-checkbox.type-fancy {
  .tgl {
    display: none;
  }

  .ghost-tgl {
    display: none;
  }

  .tgl, .tgl:after, .tgl:before, .tgl *, .tgl *:after, .tgl *:before, .tgl + .tgl-btn {
    box-sizing: border-box;
  }

  .tgl::-moz-selection,
  .tgl:after::-moz-selection,
  .tgl:before::-moz-selection,
  .tgl *::-moz-selection,
  .tgl *:after::-moz-selection,
  .tgl *:before::-moz-selection,
  .tgl + .tgl-btn::-moz-selection {
    background: none;
  }

  .tgl::selection,
  .tgl:after::selection,
  .tgl:before::selection,
  .tgl *::selection,
  .tgl *:after::selection,
  .tgl *:before::selection,
  .tgl + .tgl-btn::selection {
    background: none;
  }

  .tgl + .tgl-btn {
    @include vr($width: 3, $height: 1);
    outline: 0;
    display: block;
    position: relative;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  .tgl + .tgl-btn:after, .tgl + .tgl-btn:before {
    position: relative;
    display: block;
    content: "";
    width: 35%;
    height: 100%;
  }

  .tgl + .tgl-btn:after {
    left: 0;
  }

  .tgl + .tgl-btn:before {
    display: none;
  }

  .tgl:checked + .tgl-btn:after {
    left: 50%;
  }

  .tgl:disabled + .tgl-btn {
    opacity: 0.3;
  }

  .tgl-btn {
  }

  .tgl-square + .tgl-btn {
    background: $tgl-bg;
    border-radius: $tgl-radius;
    transition: all .4s ease;
    border: 2px solid $tgl-on-color;
    color: $tgl-on-color;
    overflow: hidden;

    .on-label {
      left: 0;
      opacity: 0;
    }

    .off-label {
      opacity: 1;
      left: 65%;
    }

    .on-label, .off-label {
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
      color: inherit;
      font-weight: 600;
      text-transform: uppercase;
      will-change: left;
      transition: left $tgl-switcher-transition-time ease-in-out,
      opacity $tgl-switcher-transition-time ease-in-out;
    }
  }

  .tgl-square + .tgl-btn:after {
    left: -2px;
    position: absolute;
    border-right: 2px solid $tgl-on-color;
    border-left: 2px solid $tgl-on-color;
    border-radius: $tgl-radius 0 0 $tgl-radius;
    background: $tgl-switcher;
    will-change: left;
    transition: left $tgl-switcher-transition-time ease-in-out,
    border-radius $tgl-switcher-transition-time ease-in-out;
  }

  .tgl-square:checked + .tgl-btn:after {
    left: calc(65% + 2px);
    border-radius: $tgl-radius;
  }

  .tgl-square:checked + .tgl-btn {
    background: $tgl-on-color;
    color: $tgl-bg;

    .off-label {
      left: 100%;
      opacity: 0;
    }

    .on-label {
      opacity: 1;
      left: 35%;
    }
  }
}
</style>
