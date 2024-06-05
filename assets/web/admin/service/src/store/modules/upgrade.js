/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

import { fetchDisallowedModulesData, fetchUpgradePageData, fetchWavesInfo } from '../../api/upgrade';

export default class UpgradeModule {
  createModule() {
    return {
      namespaced: true,
      state() {
        return {
          advancedMode: false,
          count: 0,
          disallowed: [],
          disallowedFetched: false,
          loading: false,
          loaded: false,
          unselected: [],
          upgrade: [],
          waves: [],
        };
      },
      mutations: {
        SET_ADVANCED_MODE(state, value) {
          state.advancedMode = value;
          state.unselected = [];
        },
        SET_ENTRY_ADVANCED_STATE(state, { id, value }) {
          let unselected = state.unselected;

          if (value) {
            unselected = _.without(unselected, id);
          } else {
            unselected.push(id);
          }

          state.unselected = unselected;
        },
        SET_COUNT(state, value) {
          state.count = value;
        },
        SET_DISALLOWED(state, data) {
          state.disallowed = data;
        },
        SET_DISALLOWED_FETCHED(state, value) {
          state.disallowedFetched = value;
        },
        SET_LOADING(state, value) {
          state.loading = value;
        },
        SET_LOADED(state, value) {
          state.loaded = value;
        },
        SET_UPGRADE(state, data) {
          state.upgrade = data;
        },
        SET_WAVES(state, data) {
          state.waves = data;
        },
      },
      actions: {
        fetchAvailableUpgrades({ commit, dispatch }, { shade, upgradeType } = {}) {
          if (shade) {
            commit('SET_LOADING', true);
          }

          const additionalInfo = { withAdditionalInfo: true };

          if (window.xliteConfig.display_upgrade_notifications) {
            fetchUpgradePageData(additionalInfo).then(async (data) => {
              const result = await data;

              const minor = [];
              const major = [];
              const delayed = [];

              _.filter(
                result.upgrade,
                (item) => {
                  const moduleData = { id: item.moduleId, enabled: item.moduleData.enabled };

                  if (Object.keys(item.delayedUpgrade).length > 0) {
                    delayed.push(
                      {
                        ...item.delayedUpgrade,
                        ...moduleData,
                      },
                    );
                  }

                  if (Object.keys(item.buildUpgrade).length > 0) {
                    minor.push(
                      {
                        ...item.buildUpgrade,
                        ...moduleData,
                      },
                    );
                  }

                  if (Object.keys(item.minorUpgrade).length > 0) {
                    major.push(
                      {
                        ...item.minorUpgrade,
                        ...moduleData,
                      },
                    );
                  }

                  if (Object.keys(item.majorUpgrade).length > 0) {
                    major.push(
                      {
                        ...item.majorUpgrade,
                        ...moduleData,
                      },
                    );
                  }
                },
              );

              let upgrade = {};

              if (delayed.length) {
                upgrade = { ...upgrade, delayed };
              }

              if (minor.length) {
                upgrade = { ...upgrade, minor };
              }

              if (major.length) {
                upgrade = { ...upgrade, major };
              }

              commit('SET_UPGRADE', upgrade);
              commit('SET_COUNT', result.totalItems);

              if (shade) {
                commit('SET_LOADING', false);
                commit('SET_LOADED', true);
              }

              if (upgradeType !== undefined) {
                dispatch(
                  'scenarios/upgrade/fillUpgradeScenario',
                  { upgradeType },
                  { root: true },
                );
              }
            }).catch((error) => {
              // eslint-disable-next-line no-console
              console.error('There was an error:', error);
            });
          }
        },
        fetchDisallowedModules({ commit }) {
          fetchDisallowedModulesData().then(async (data) => {
            const result = await data;

            const disallowed = result.disallowed;

            commit('SET_DISALLOWED', disallowed);
            commit('SET_DISALLOWED_FETCHED', true);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
        fetchWavesCommonInfo({ commit }) {
          fetchWavesInfo().then(async (data) => {
            const result = await data;

            let waves = {};

            _.filter(result.waves, (wave) => {
              if (wave.name === 'Developer') {
                waves = { ...waves, ...{ developer: { id: wave.id, name: wave.name } } };
              }

              if (wave.name === 'Merchant') {
                waves = { ...waves, ...{ merchant: { id: wave.id, name: wave.name } } };
              }
            });

            commit('SET_WAVES', waves);
          }).catch((error) => {
            // eslint-disable-next-line no-console
            console.error('There was an error:', error);
          });
        },
      },
      getters: {
        getSelected: state => (type) => {
          const unselected = JSON.parse(JSON.stringify(state.unselected));
          const upgrade = JSON.parse(JSON.stringify(state.upgrade));

          const selected = (unselected.length === 0)
            ? upgrade[type]
            : _.filter(upgrade[type], ({ id }) => !_.includes(unselected, id));

          return selected;
        },
      },
    };
  }
}
