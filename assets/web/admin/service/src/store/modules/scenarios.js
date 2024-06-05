/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import ScenarioModule from './scenario';
import { MODULE_STATES } from '../../../src/constants';

export default class ScenariosModule {
  createModule() {
    return {
      namespaced: true,
      modules: {
        common: (new ScenarioModule('common')).createModule(),
        upgrade: (new ScenarioModule('upgrade')).createModule(),
      },
      actions: {
        fetch({ state, dispatch }, type = null) {
          if (typeof state[type] === 'undefined') {
            this.registerModule(['scenarios', type], (new ScenarioModule(type)).createModule());
          }

          return dispatch(`${type}/fetch`);
        },
        toggleModuleState: {
          root: true,
          handler({ dispatch }, payload) {
            const module = payload.module;
            const stateToSet = payload.stateToSet;

            dispatch('modulesData/setModuleState', { module, stateToSet }, { root: true });
            dispatch('common/addTransition', { module, stateToSet });
            dispatch('common/update');
          },
        },
        removeModule: {
          root: true,
          handler({ dispatch }, payload) {
            const module = payload.module;
            const stateToSet = MODULE_STATES.REMOVED;

            dispatch('modulesData/setModuleState', { module, stateToSet }, { root: true });
            dispatch('common/addTransition', { module, stateToSet });
            dispatch('common/update');
          },
        },
      },
    };
  }
}
