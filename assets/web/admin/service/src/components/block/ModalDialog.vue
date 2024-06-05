<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<template>
  <modal
    :name="identifier"
    :width="width"
    :adaptive="true"
    :scrollable="true"
    :click-to-close="closable"
    height="auto"
    transition="fade"
    @before-open="beforeOpened"
    @before-close="beforeClosed">
    <div
      :class="dialogClass"
      class="modal-dialog">
      <div class="dialog-title">
        <div class="title-content">
          <slot
            v-if="params.title"
            :title="params.title"
            :params="params"
            :hide="hide"
            name="title">
            <i18n
              :path="title"
              tag="h2"
              class="heading"/>
          </slot>
          <a
            v-if="closable"
            class="close"
            @click="hide">
            <icon
              name="cross"
              width="14px"/>
          </a>
        </div>
      </div>
      <div
        v-scroll="onScroll"
        ref="content"
        class="dialog-content">
        <slot :params="params">
          <div class="dialog-text">{{ $t(params.text, params.textArgs) }}</div>
          <ul class="dialog-list">
            <li
              v-for="item in params.list"
              :key="item.id">{{ item }}
            </li>
          </ul>
        </slot>
      </div>
      <div class="dialog-buttons">
        <div class="buttons-content">
          <slot
            :buttons="buttons"
            :click="click"
            :params="params"
            name="buttons">
            <div
              v-for="(button, i) in buttons"
              :key="i"
              class="button-wrapper">
              <button
                :key="i"
                :class="button.class"
                @click.stop="click(button, $event)"
                v-html="$t(button.title)"/>
            </div>
          </slot>
        </div>
      </div>
    </div>
  </modal>
</template>

<script>
import Icon from './Icon';

export default {
  name: 'ModalDialog',
  components: {
    Icon,
  },
  props: {
    identifier: {
      type: String,
      default: '',
    },
    width: {
      type: Number,
      default: 600,
    },
    closable: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      params: {
        title: '',
      },
      scrollable: false,
      scrolledTop: true,
      scrolledBottom: false,
    };
  },
  computed: {
    dialogClass() {
      return {
        [this.identifier]: true,
        scrollable: this.scrollable,
        'scrolled-top': this.scrolledTop,
        'scrolled-bottom': this.scrolledBottom,
      };
    },
    buttons() {
      return this.params.buttons || [];
    },
    title() {
      return this.params.title || '';
    },
  },
  updated() {
    _.once(() => this.$nextTick(() => {
      setTimeout(() => {
        const content = this.$refs.content;

        this.scrollable = content ? (content.clientHeight < content.scrollHeight) : false;
      }, 0);
    }))();
  },
  methods: {
    onScroll(event, position) {
      if (position.scrollTop === 0) {
        this.scrolledTop = true;
        this.scrolledBottom = false;
      } else if ((event.target.clientHeight + position.scrollTop) === event.target.scrollHeight) {
        this.scrolledTop = false;
        this.scrolledBottom = true;
      } else {
        this.scrolledTop = false;
        this.scrolledBottom = false;
      }
    },
    beforeOpened(event) {
      window.addEventListener('keyup', this.onKeyUp);
      this.params = event.params || {};
      this.$emit('before-opened', event);
    },

    beforeClosed(event) {
      window.removeEventListener('keyup', this.onKeyUp);
      this.params = {};
      this.scrolledTop = true;
      this.scrolledBottom = false;
      this.$emit('before-closed', event);

      if (event.name === 'module-details') {
        this.$store.commit('singleModule/SET_ID', null);
      }
    },

    click(button) {
      if (button && typeof button.handler === 'function') {
        button.handler({});
      } else {
        this.hide();
      }
    },

    hide() {
      this.$modal.hide(this.identifier);
    },

    onKeyUp(event) {
      if (event.which === 13 && this.buttons.length > 0) {
        const buttonIndex = (this.buttons.length === 1)
          ? 0
          : this.buttons.findIndex(button => button.default);
        if (buttonIndex !== -1) {
          this.click(buttonIndex, event, 'keypress');
        }
      }
    },
  },
};
</script>

<style lang="scss">
@import '../../stylesheets/common';

.modal-dialog {
  .v--modal & {
    width: 100%;
    margin-top: 0;
    margin-bottom: 0;
  }

  display: flex;
  flex-direction: column;
  height: 100%;
  max-height: 95vh;
  border-radius: $border-radius;

  h2.heading {
    margin-bottom: 0;
  }

  .dialog-title {
    position: relative;
    flex: 1 0 auto;

    .title-content {
      @include vr($padding: 0 1);
      position: relative;
      background-color: $body-bg;
    }

    .close {
      @include close-icon;
      position: absolute;
      right: $rhythmic-unit;
      top: $rhythmic-unit * .5;
      opacity: 1;
    }
  }

  .dialog-form {
    .row {
      @include vr($margin-top: 1);
      display: flex;
      align-items: center;

      & > input {
        min-width: 250px;
      }

      & > span {
        margin-left: 1rem;
      }

      & > button {
        margin-left: 1rem;
      }
    }

    .links {
      @include vr($margin-top: 1);

      & > * + * {
        @include vr($margin-top: .5);
      }
    }
  }

  .dialog-content {
    @include vr($padding: 1);
    flex: 1 2 auto;
    overflow: auto;

    & > .dialog-list {
      margin-bottom: 0;
      overflow-wrap: break-word;
    }
  }

  .dialog-buttons {
    position: relative;

    .buttons-content {
      @include vr($padding: .5 1);
      transition: padding .15s ease-in-out;
      display: flex;
      flex: 0 0 auto;
      justify-content: flex-end;
      align-items: center;
      width: 100%;
      position: relative;
      background-color: $body-bg;

      * {
        font-size: $font-size-small;
        line-height: $line-height-small;
      }

      button * {
        font-size: $font-size-base;
        line-height: $line-height-base;
      }

      & > * + * {
        @include vr($margin-left: 1);
      }
    }
  }

  &.scrollable {
    .dialog-title:before {
      @include vr($margin-left: .5, $margin-right: .5);
      content: '';
      opacity: 1;
      transition: $basic-slower-transition;
      position: absolute;
      right: 0;
      bottom: 0;
      left: 0;
      border-radius: $rhythmic-unit * 2;
      box-shadow: $modal-box-shadow;
    }

    &.scrolled-top .dialog-title:before {
      opacity: 0;
    }

    .dialog-buttons:before {
      @include vr($margin-left: .5, $margin-right: .5);
      content: '';
      opacity: 1;
      transition: $basic-slower-transition;
      position: absolute;
      top: 0;
      right: 0;
      left: 0;
      border-radius: $rhythmic-unit * 2;
      box-shadow: $modal-box-shadow;
    }

    .buttons-content {
      @include vr($padding-top: .5, $padding-bottom: .5);

      display: flex;

      .button-wrapper + .button-wrapper {
        @include vr($margin-left: 1);
      }
    }

    &.scrolled-bottom .dialog-buttons:before {
      opacity: 0;
    }
  }
}
</style>
