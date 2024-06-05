/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import { fetchStoreInfo } from '../../api/info';

export default class TechInfoModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          response: null,
          techInfo: null,
          store: null,
          modules: null,
          loaded: false,
        };
      },
      mutations: {
        SET_TECH_INFO(state, value) {
          const info = (value && value['hydra:member'] && value['hydra:member'][0])
            ? value['hydra:member'][0]
            : {};
          state.response = value;
          state.techInfo = info;
          state.store = info.store ? info.store : {};
          state.modules = info.modules ? info.modules : {};
          state.loaded = true;
        },
      },
      actions: {
        fetchStoreInfo({ commit }) {
          fetchStoreInfo().then(async (data) => {
            const result = await data;
            commit('SET_TECH_INFO', result);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
    };
  }
}
