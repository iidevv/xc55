/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import { fetchModuleById } from '../../api/modules';
import { getModuleWithIcons } from '../../utils';

export default class ModuleModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          id: null,
          module: null,
        };
      },
      mutations: {
        SET_ID(state, value) {
          state.id = value;
        },
        SET_MODULE(state, data) {
          state.module = data;
        },
        SET_MODULE_SCENARIO_STATE(state, { module, stateToSet }) {
          if (state.module && state.module.id === module.id) {
            state.module.scenarioState = stateToSet || state.module.scenarioState;
          }
        },
      },
      actions: {
        async fetchModuleData({ commit, state }, { stateToSet, messages }) {
          await fetchModuleById(state.id).then(async (data) => {
            const result = await data;

            const module = getModuleWithIcons(result.module);

            module.scenarioState = stateToSet || module.state;

            // Adjust type
            if (!Object.keys(module.warning).length) {
              module.warning = {};
            }

            const moduleData = { ...module, messages };

            commit('SET_MODULE', moduleData);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
      getters: {
        getModule: state => state.module,
      },
    };
  }
}
