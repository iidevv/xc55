/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import { fetchModulesPage } from '../../api/modules';
import { MODULE_STATES } from '../../../src/constants';

const defaultLimit = 24;
const initPage = 1;

const prepareFetchParams = (
  state,
  transitions,
  findEnabledCustom = false,
  fetchCustomPage = false,
) => {
  let sortByParam = { 'order[metaData->moduleName]': 'asc' };


  if (state.filter.sortBy) {
    if (state.filter.sortBy === 'enabled') {
      sortByParam = { ...sortByParam, ...{ state: MODULE_STATES.ENABLED } };
    } else if (state.filter.sortBy === 'disabled') {
      sortByParam = { ...sortByParam, ...{ 'state[0]': MODULE_STATES.INSTALLED, 'state[1]': MODULE_STATES.NOT_INSTALLED } };
    } else if (state.filter.sortBy === 'recent') {
      sortByParam = { state: MODULE_STATES.ENABLED, 'order[enabledDate]': 'desc' };
    }
  }

  const isCustom = findEnabledCustom || fetchCustomPage
    ? { isCustom: true }
    : {};

  if (findEnabledCustom) {
    sortByParam = { ...sortByParam, ...{ state: MODULE_STATES.ENABLED } };
  }

  const search = state.filter.search
    ? { search: state.filter.search }
    : {};

  return {
    itemsPerPage: state.filter.limit,
    page: state.filter.page,
    transitions: JSON.stringify(transitions),
    ...sortByParam,
    ...search,
    ...isCustom,
  };
};

export default class ModulesModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          loading: false,
          loaded: false,
          filter: {
            enabled: '',
            installed: true,
            language: 'en',
            limit: defaultLimit,
            page: initPage,
            search: '',
          },
          modules: [],
          count: 0,
          customModulesCount: 0,
        };
      },
      mutations: {
        SET_LOADING(state, value) {
          state.loading = value;
        },
        SET_LOADED(state, value) {
          state.loaded = value;
        },
        SET_FILTER(state, data) {
          const filterData = data;

          if (data.limit === undefined) {
            filterData.limit = defaultLimit;
          }

          state.filter = filterData;
        },
        SET_MODULES(state, data) {
          state.modules = data;
        },
        SET_COUNT(state, data) {
          state.count = data;
        },
        SET_CUSTOM_MODULES_COUNT(state, data) {
          state.customModulesCount = data;
        },
        SET_MODULE_SCENARIO_STATE(state, { module, stateToSet }) {
          state.modules = state.modules.map((item) => {
            const result = item;

            if (module.id === item.id) {
              result.scenarioState = stateToSet || item.scenarioState;
            }

            return result;
          });
        },
      },
      actions: {
        setModuleState({ commit }, { module, stateToSet }) {
          commit('modulesData/SET_MODULE_SCENARIO_STATE', { module, stateToSet }, { root: true });
          commit('singleModule/SET_MODULE_SCENARIO_STATE', { module, stateToSet }, { root: true });
        },
        fetchModules({ commit, state }, { transitions, shade, fetchCustom } = {}) {
          if (shade) {
            commit('SET_LOADING', true);
          }

          const queryCustomPageParam = window.location.hash.substring(2).split(/[/|?|&]/g)[0] === 'custom-modules';

          const params = prepareFetchParams(state, transitions, fetchCustom, queryCustomPageParam);

          fetchModulesPage(params).then(async (data) => {
            const result = await data;

            commit('SET_MODULES', result.modules);
            commit('SET_COUNT', result.totalItems);

            commit('SET_CUSTOM_MODULES_COUNT',
              fetchCustom || queryCustomPageParam
                ? result.totalItems
                : 0,
            );

            if (shade) {
              commit('SET_LOADING', false);
            }

            commit('SET_LOADED', true);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
      getters: {
        getCount: state => state.count,
      },
    };
  }
}
