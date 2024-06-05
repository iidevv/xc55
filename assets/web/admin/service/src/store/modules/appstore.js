/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../../config/app-config';

/* global define */
export default class AppStoreModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          id: '',
          url: `${appConfig.extAppStoreUrl}`,
        };
      },
      mutations: {
        SET_STORE_ID (state, value) {
          state.id = value;
        },
      },
      actions: {
        async fetchStoreId({ commit }) {
          define('shopIdentifier', ['common/coreLicense'], (coreLicense) => {
            commit('SET_STORE_ID', coreLicense.shopIdentifier);
          });
        },
      },
      getters: {
        getStoreId: state => state.id,
        getStoreUrl: state => state.url + (state.id ? `?xc5_shop_identifier=${state.id}` : ''),
      },
    };
  }
}
