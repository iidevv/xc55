/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import appConfig from '../../../config/app-config';
import {
  createNonPersistentScenario,
  createScenario,
} from '../../api/scenario';

function getLastStoredTransitions(type) {
  return JSON.parse(
    sessionStorage.getItem(`scenario.${type}.${appConfig.session}`),
  );
}

function setLastStoredTransitions(type, value) {
  if (!value) {
    sessionStorage.removeItem(`scenario.${type}.${appConfig.session}`);
  } else {
    sessionStorage.setItem(`scenario.${type}.${appConfig.session}`, JSON.stringify(value));
  }
}

export default class ScenarioModule {
  constructor(type) {
    this.type = type;
  }

  createModule() {
    const self = this;

    return {
      namespaced: true,
      state() {
        return {
          type: self.type,
          transitions: {},
          alreadyStartedScenarioId: null,
          miniCartDisplay: false,
          miniCartDisplayHasNeverBeenShown: true,
        };
      },
      mutations: {
        SET_STATE(state, payload) {
          Object.assign(state, payload);
          setLastStoredTransitions(state.type, payload.transitions);
        },
        SET_MINI_CART_DISPLAY(state, isOpened) {
          state.miniCartDisplay = isOpened;

          if (isOpened) {
            state.miniCartDisplayHasNeverBeenShown = false;
          }
        },
        SWITCH_MINI_CART_DISPLAY(state) {
          state.miniCartDisplay = !state.miniCartDisplay;

          if (state.miniCartDisplay) {
            state.miniCartDisplayHasNeverBeenShown = false;
          }
        },
      },
      actions: {
        fetch({ commit }) {
          const transitions = getLastStoredTransitions(self.type) || {};

          commit('SET_STATE', { transitions });
        },

        update({ commit, state, dispatch }) {
          createNonPersistentScenario(state.transitions).then(async (data) => {
            const result = await data;

            commit('SET_STATE', result);
            dispatch('modulesData/fetchModules', { transitions: state.transitions, shade: false }, { root: true });
          }).catch((error) => {
            this.errorMessage = error;
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },

        fillUpgradeScenario({ commit, rootGetters }, { upgradeType }) {
          const transitions = rootGetters['upgrades/getSelected'](upgradeType);

          commit('SET_STATE', { transitions });
        },

        clear({ commit }) {
          commit('SET_STATE', {
            transitions: [],
            alreadyStartedScenarioId: null,
          });
        },

        discard({ dispatch }) {
          dispatch('clear');
          dispatch('update');
        },

        addTransition({ commit, state }, { module, stateToSet }) {
          const transitions = state.transitions;

          if (stateToSet === module.state) {
            delete transitions[module.id];
          } else {
            transitions[module.id] = {
              stateToSet,
              moduleName: module.moduleName,
            };
          }

          commit('SET_STATE', { transitions });
        },

        rebuild({ commit, state, dispatch }) {
          createScenario(state.type, state.transitions).then(async (data) => {
            const response = await data;

            if (response.errorType === 'alreadyStarted') {
              commit('SET_STATE', {
                alreadyStartedScenarioId: response.scenarioId,
              });

              return Promise.reject(response.errorType);
            }

            if (data.id) {
              dispatch('clear');

              const publicDir = appConfig.publicDir !== '' ? `/${appConfig.publicDir}` : '';
              const encodedCurrentUrl = encodeURIComponent(window.location.href);
              const redirectPage = (state.type === 'common')
                ? 'rebuild.html'
                : 'upgrade.html';

              window.location.href =
                // eslint-disable-next-line max-len
                `${appConfig.url + publicDir}/${redirectPage}?scenarioId=${data.id}&returnURL=${encodedCurrentUrl}`;
            }

            return true;
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
      getters: {
        getScenario: state => state,
      },
    };
  }
}
