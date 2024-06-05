<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownAttribute, CheckEmptyScriptTag -->
<template>
  <modal-dialog
    :width="800"
    identifier="module-details">

    <template
      v-slot:title="{ title }"
      v-if="module">
      <i18n
        :path="module.moduleName"
        tag="h2"
        class="heading"/>
    </template>
    <template
      v-slot="{ params }"
      v-if="module">

      <module-alert
        v-if="hasWarning"
        :messages="module.messages"
        :warning="module.warning"/>

      <div class="module-details-content">

        <div class="info">
          <div class="image">
            <!-- TODO: https://sellerlabs.atlassian.net/browse/ECOM-1345 -->
            <img
              v-if="module.icon"
              :src="module.icon"
              :alt="module.name">
            <img
              v-else
              :alt="module.name"
              src="../../stylesheets/addon_default.png">
          </div>
          <div class="params">
            <div class="author">
              <i18n
                path="Author"
                class="label"/>
              :
              <span
                v-if="module.authorPageUrl"
                class="value">
                <a :href="module.authorPageUrl">{{ module.authorName }}</a>
              </span>
              <span
                v-else
                class="value">{{ module.authorName }}</span>
            </div>

            <div class="version">
              <i18n
                path="Version"
                class="label"/>
              :
              <span
                class="value"
                v-html="module.version"/>

              <span>
                <i18n
                  v-if="module.state === MODULE_STATES.ENABLED"
                  path="Enabled"
                  class="availability state enabled"/>
                <i18n
                  v-else
                  path="Disabled"
                  class="availability state disabled"/>
              </span>
            </div>
            <div
              v-if="module.pageUrl"
              class="marketplace-link">
              <i18n
                :href="module.pageUrl"
                path="module.view_details.marketplace"
                tag="a-external"/>
            </div>
          </div>
        </div>
        <div class="description">
          <i18n
            path="Description"
            tag="h3"/>
          <div>{{ module.description }}</div>
        </div>
      </div>
    </template>
    <template
      v-slot:buttons="{ params }"
      v-if="module">
      <installed-module-actions :module="module"/>
    </template>
  </modal-dialog>
</template>

<script>
import { mapState } from 'vuex';
import ModuleAlert from '../item/ModuleAlert';
import InstalledModuleActions from '../item/action/InstalledModuleActions';
import ModalDialog from '../block/ModalDialog';
import { MODULE_STATES } from '../../../src/constants';

export default {
  name: 'ModuleDetailsDialog',
  components: {
    InstalledModuleActions,
    ModalDialog,
    ModuleAlert,
  },
  data() {
    return {
      MODULE_STATES: Object.freeze(MODULE_STATES),
    };
  },
  computed: {
    ...mapState({
      module(state) {
        return state.singleModule.module;
      },
      moduleId(state) {
        return state.singleModule.id;
      },
    }),
    hasWarning() {
      return !_.isEmpty(this.module.warning) || !_.isEmpty(this.module.messages);
    },
  },
  watch: {
    module(module) {
      if (module !== null && this.moduleId) {
        this.$modal.show('module-details', { title: module.moduleName });
      } else {
        this.$modal.hide('module-details');
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import '../../stylesheets/common';

.modal-dialog.module-details {
  .module-alert {
    @include vr($margin-bottom: .5);
    position: static;
  }

  .info {
    @include vr($margin-bottom: 2);
    display: flex;
  }

  .image {
    @include module-image;
  }

  .tags {
    @include module-tags;
  }

  .description {
    h3 {
      @include vr($margin: 0 0 .25);
    }
  }

  .marketplace-link {
    width: 100%;
  }

  .rating-info {
    display: flex;
    align-items: center;

    .rating {
      @include vr($margin-left: .5);
      display: flex;
      align-items: center;
    }
  }

  .author,
  .version {
    display: flex;
    align-items: center;

    .value {
      @include vr($margin-left: .25);
    }
  }

  .module-price {
    display: flex;
    font-weight: $font-weight-bold;

    .label {
      @include vr($margin-right: .25);
    }

    &.on-sale {
      color: $error_tips;
    }

    .orig-price .price {
      text-decoration: line-through;
      color: $border;
      @include vr($margin-left: .25);
    }
  }

  .purchase {
    @include purchase-button-group;

    a:hover {
      cursor: pointer;
    }
  }

  .availability {
    font-weight: $font-weight-bold;
    @include vr($margin-left: .25);

    &.installed,
    &.enabled {
      color: $boring-green;
    }

    &.disabled {
      color: $tips;
    }

    &.free {
      color: $boring-green;
    }
  }

  .params {
    @include vr($margin-left: .5);

    .label {
      font-weight: $font-weight-bold;
      color: inherit;
      font-size: inherit;
      line-height: inherit;
      padding: 0;
    }

    > .marketplace-link {
      @include vr($margin-top: .8)
    }
  }

  .installed-module.actions {
    .checkbox-enable.disabled {
      display: none;
    }

    .upgrade-request button {
      margin: 0;
    }

    .module-alert {
      display: none;
    }

    * {
      font-size: $font-size-small;
      line-height: $line-height-small;
    }

    button * {
      font-size: $font-size-base;
      line-height: $line-height-base;
    }

    & > * + *:not(.upgrade-request) {
      &:before {
        content: '';
        box-shadow: 0 0 0 .5px $input-border-color;
        width: 1px;
        margin-left: 1rem;
        margin-right: 1rem;
        line-height: $line-height-small;
      }
    }
  }

  .install-later {
    @include vr($margin-right: 1);
    order: -1;
  }
}
</style>
