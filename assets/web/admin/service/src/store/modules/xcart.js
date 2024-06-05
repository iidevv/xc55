/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

export default class XCartModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return window.xcart;
      },
      getters: {
        xcart: state => state,
      },
    };
  }
}
