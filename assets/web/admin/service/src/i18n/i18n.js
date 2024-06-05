/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// import Vue from 'vue';
import VueI18n from 'vue-i18n';
import i18nInfo from './index';

// eslint-disable-next-line no-undef
Vue.use(VueI18n);

const messages = i18nInfo.getMessages();
const locale = 'en';

const defaultImpl = VueI18n.prototype.getChoiceIndex;
VueI18n.prototype.getChoiceIndex = (choice, choicesLength) => {
  if (this.locale !== 'ru') {
    // eslint-disable-next-line prefer-rest-params
    return defaultImpl.apply(this, arguments);
  }
  if (choice === 0) {
    return 0;
  }
  const teen = (choice > 10) && (choice < 20);
  const endsWithOne = (choice % 10) === 1;
  if (choicesLength < 4) {
    return (!teen && endsWithOne) ? 1 : 2;
  }
  if (!teen && endsWithOne) {
    return 1;
  }
  if (
    !teen
    && choice % 10 >= 2
    && choice % 10 <= 4
  ) {
    return 2;
  }
  return (choicesLength < 4) ? 2 : 3;
};

const i18n = new VueI18n({
  locale: messages[locale] ? locale : i18nInfo.getFirstLocale(),
  messages,
  dateTimeFormats: {
    en: {
      short: {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      },
      long: {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
      },
    },
  },
});

export default i18n;
