<!--
Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
See https://www.x-cart.com/license-agreement.html for license details.
-->

<!--suppress HtmlUnknownAttribute, CheckEmptyScriptTag -->
<template>
  <modal-dialog
    :width="800"
    identifier="activate-license-key">

    <template
      v-slot:title="{ title }">
      <i18n
        :path="title"
        tag="h2"
        class="heading"/>
    </template>

    <div
      v-if="text"
      class="activate-license-key-text">
      {{ text }}
    </div>

    <div class="activate-license-key-block">
      <input
        v-model="licenseKey"
        :placeholder="$t('Enter your license key')"
        :disabled="inputDisabled"
        class="form-control">

      <button
        :class="registeredClass"
        class="btn regular-button"
        @click="registerLicenseKey">
        {{ submitLicenseButtonTitle }}
      </button>

      <span
        v-if="isTrial"
        class="or">
        {{ $t('or') }}
      </span>

      <button
        v-if="isTrial"
        class="regular-main-button"
        @click="contactXcart">
        {{ $t('Contact X-Cart') }}
      </button>
    </div>

  </modal-dialog>
</template>

<script>
import { mapState } from 'vuex';
import ModalDialog from '../block/ModalDialog';
import { openTab } from '../../utils';

/* global define */
export default {
  name: 'ActivateLicenseKeyDialog',
  components: {
    ModalDialog,
  },
  data() {
    return {
      activateKeyHandler: null,
      licenseKey: '',
      inputDisabled: false,
    };
  },
  computed: {
    ...mapState({
      licenses(state) {
        return state.licenses;
      },
    }),
    locale() {
      return this.$i18n.locale;
    },
    isTrial() {
      return this.licenses.coreInfo.isTrial;
    },
    text() {
      return this.isTrial
        ? this.$t('Please enter your license key in the field below or contact our Solution Advisors to get one.')
        : this.$t('To activate your X-Cart or commercial module license, enter your license key into the field below and click Activate.');
    },
    registeredClass() {
      return {
        'regular-main-button': !this.isTrial,
      };
    },
    submitLicenseButtonTitle() {
      return this.isTrial ? this.$t('Register') : this.$t('Activate');
    },
  },
  created() {
    define('activateKeyHandler', ['common/activateKeyHandler'], (activateKeyHandler) => {
      this.activateKeyHandler = activateKeyHandler;
    });
  },
  methods: {
    registerLicenseKey() {
      const self = this;
      self.inputDisabled = true;

      this.activateKeyHandler(
        () => {
          self.inputDisabled = false;
          self.licenseKey = '';
          _.delay(() => self.$modal.hide('activate-license-key'), 300);

          this.$store.dispatch('licenses/fetchLicenses');
          this.$store.dispatch('upgrades/fetchDisallowedModules');
        },
        () => {
          self.inputDisabled = false;
        },
      ).handle({ licenseKey: this.licenseKey });
    },
    contactXcart() {
      openTab(`http://www.x-cart.com/contact-us.html?sl=${this.locale}&utm_source=XC5admin&utm_medium=main&utm_campaign=XC5admin`);
    },
  },
};
</script>

<style lang="scss" scoped>
@import '../../stylesheets/common';

.activate-license-key-block {
  display: flex;
  align-items: center;

  input {
    width: $rhythmic-unit * 13;
    margin-right: .5 * $rhythmic-unit;
  }
}

.or {
  margin-left: .5 * $rhythmic-unit;
  margin-right: .5 * $rhythmic-unit;
}

.activate-license-key-text {
  margin: 0 0 $rhythmic-unit;
}
</style>
