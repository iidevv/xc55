/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import { fetchCoreLicenseInfo, fetchStoreLicenses } from '../../api/license';

export default class UpgradeModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          coreInfo: [],
          info: [],
        };
      },
      mutations: {
        SET_CORE_INFO(state, value) {
          state.coreInfo = value;
        },
        SET_INFO(state, value) {
          state.info = value;
        },
      },
      actions: {
        fetchCoreInfo({ commit }) {
          fetchCoreLicenseInfo().then(async (data) => {
            const result = await data;

            commit('SET_CORE_INFO', result);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
        fetchLicenses({ commit }) {
          fetchStoreLicenses({}).then(async (data) => {
            const result = await data;

            commit('SET_INFO', result.licenses);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
    };
  }
}
