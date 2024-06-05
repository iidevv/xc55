<!--suppress HtmlUnknownTarget -->
<template>
  <div
    :class="{ opened: opened}"
    class="dropdown-select">
    <div
      ref="toggle"
      class="dropdown-label"
      tabindex="-1"
      @click="toggle">
      <span class="label"><slot>{{ translate ? $t(label) : label }}</slot></span>
      <span
        v-if="selectedLabel"
        class="selected">{{ translate ? $t(selectedLabel) : selectedLabel }}</span>
      <span
        v-if="iconType === 'caret'"
        class="caret-container">
        <span class="caret"/>
      </span>
      <span
        v-else
        class="chevron">
        <i class="fa fa-angle-right"/>
      </span>
    </div>
    <div
      v-show="opened"
      ref="menu"
      class="dropdown-options">
      <ul :style="listStyle">
        <li
          v-for="(option, index) in options"
          :key="index"
          :selected="isSelected(option)">
          <hr v-if="option.separator">
          <input
            v-if="typeof option.value !== 'undefined'"
            :value="option.value"
            :name="id"
            :id="id + option.value"
            :checked="option.value === value"
            type="radio"
            @change="onChange">
          <label
            v-if="option.label && typeof option.value !== 'undefined'"
            :for="id + option.value">
            {{ translate ? $t(option.label) : option.label }}
          </label>
          <div v-else-if="option.label && !option.link">
            {{ translate ? $t(option.label) : option.label }}
          </div>
          <a
            v-else-if="option.link && !option.external"
            :href="option.link">
            {{ translate ? $t(option.label) : option.label }}
          </a>
          <a-external
            v-else-if="option.link && option.external"
            :href="option.link">
            {{ translate ? $t(option.label) : option.label }}
          </a-external>
          <div
            v-if="option.description"
            class="description">
            {{ translate ? $t(option.description) : option.description }}
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>


<script>
import Select from './Select';

export default {
  name: 'DropdownSelect',
  extends: Select,
  props: {
    iconType: {
      type: String,
      default: '',
    },
    columns: {
      type: Number,
      default: 1,
    },
    translate: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      opened: false,
    };
  },
  computed: {
    listStyle() {
      const attrs = {};

      if (this.columns) {
        attrs['column-count'] = this.columns;
      }

      return attrs;
    },
    selectedLabel() {
      const option = _.find(this.options, item => item.value === this.value);

      return option
        ? option.label
        : '';
    },
  },
  methods: {
    isSelected(option) {
      return typeof option.value !== 'undefined' && option.value === this.value;
    },
    onChange(...args) {
      Select.methods.onChange.apply(this, args);
      this.opened = !this.opened;
    },
    toggle() {
      this.opened = !this.opened;
    },
  },
};
</script>


<style lang="scss" scoped>
@import '../../stylesheets/common';

.dropdown-select {
  position: relative;
  display: flex;
  align-items: center;
  white-space: nowrap;

  .dropdown-label {
    cursor: pointer;
    display: flex;
    align-items: center;

    & > * {
      vertical-align: top;
    }

    span.selected {
      font-weight: $font-weight-bold;

      img {
        @include vr($height: 1);
      }
    }

    .caret-container {
      display: flex;
      align-items: center;
      justify-content: center;
      @include vr(
        $width: .5,
        $height: .5
      );
    }

    span.chevron,
    span.caret {
      display: flex;
      margin-left: .5 * $rhythmic-unit;
      transition: transform .3s ease;
    }

    &:hover, &:focus {
      color: $icon-hover;
      outline: 0;
    }

    transition: $basic-transition;

    svg[data-icon="chevron-right"] {
      @include vr(
        $line-height: 1,
        $height: .5
      );
      vertical-align: baseline;
      transition: transform .15s ease-in-out;
    }
  }

  &.opened {
    .dropdown-label {
      .chevron {
        i {
          transform: rotate(90deg);
        }
      }
    }
  }

  .dropdown-options {
    @include vr(
      $margin-top: .5,
      $padding-top: $rhythmic-unit * .5,
      $padding-bottom: $rhythmic-unit * .5,
      $padding-left: 0,
      $padding-right: 0
    );

    position: absolute;
    top: 100%;
    right: 0;

    background: $body-bg;
    color: $title-text;
    border-radius: $border-radius;
    box-shadow: $dropdown-shadow;
    z-index: 100;

    ul {
      list-style: none;
      margin: 0;
      padding: 0;

      input[type="radio"] {
        display: none;
      }

      li {
        white-space: nowrap;
        font-size: $font-size-small;

        &:last-of-type {
          margin-bottom: 0;
        }

        a {
          line-height: $line-height-small;
          padding: ($rhythmic-unit * .5) $rhythmic-unit;
          display: block;
          color: $title-text;
          text-decoration: none;

          &:hover {
            background: $btn-regular-bg-hover;
          }

          svg.icon {
            opacity: 1;
          }
        }

        hr {
          @include vr($height: 1);
          margin-left: -$margin-base * .5;
          margin-right: -$margin-base * .5;
        }

        label {
          line-height: $line-height-small;
          padding: ($rhythmic-unit * .5) $rhythmic-unit;
          width: 100%;
        }

        &[selected] {
          &, label {
            font-weight: $font-weight-bold;
          }
        }

        &:not([selected]) {
          label {
            cursor: pointer;
            transition: $basic-transition;
          }

          label:hover, label:focus {
            background: $btn-regular-bg-hover;
          }
        }

        &:last-of-type {
          margin-bottom: 0;
        }
      }
    }
  }
}
</style>
