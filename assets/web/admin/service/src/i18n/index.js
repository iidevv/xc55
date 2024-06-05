/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../config/app-config';

export default {
  getMessages() {
    const result = {};

    this.getAvailableLocales()
      .forEach((locale) => {
        // eslint-disable-next-line no-underscore-dangle
        result[locale] = this._readMessages(locale);
      });

    return result;
  },
  getAvailableLocales() {
    return appConfig.languages || ['en'];
  },
  getFirstLocale() {
    return this.getAvailableLocales()[0];
  },
  _readMessages(locale) {
    return appConfig.messages ? appConfig.messages[locale] : [];
  },
};
