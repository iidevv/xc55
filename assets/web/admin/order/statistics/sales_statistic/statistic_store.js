/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * statistic vuex store
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
define('statistic/store',
  [
    'vue/vue',
    'vue/vuex',
  ],
  function (Vue, Vuex) {
    return new Vuex.Store({
      state: {
        todayData: {},
        periodYoY: null,
        periodRevenue: null,
        periodOrders: null,
        periodData: [],
        icuDateFormat: 'MMM dd, yyyy',
        isFetchingData: false,
      },
      mutations: {
        UPDATE_DATA: function (state, value) {
          state.todayData = value.todayData;
          state.periodYoY = value.periodYoY;
          state.periodRevenue = value.periodRevenue;
          state.periodOrders = value.periodOrders;
          state.periodData = value.periodData;
          state.icuDateFormat = value.icuDateFormat;
        },

        SET_IS_FETCHING_DATA: function (state, value) {
          state.isFetchingData = value;
        }
      },
      actions: {
        updateData: function (ctx, data) {
          ctx.commit('UPDATE_DATA', data);
        },

        setIsFetchingData: function (ctx, isFetching) {
          ctx.commit('SET_IS_FETCHING_DATA', isFetching);
        },
      }
    });
  });
