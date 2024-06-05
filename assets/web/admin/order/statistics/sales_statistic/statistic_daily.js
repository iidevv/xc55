/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Daily sales statistic component (for dashboard)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('statistic/daily', ['js/vue/vue', 'statistic/store'], function (XLiteVue, Store) {
  XLiteVue.component('xlite-daily-sales-statistic', {
    store: Store,

    computed: {
      dailyOrders: function () {
        return this.$store.state.todayData.orders_count;
      },
      dailyFormattedRevenue: function () {
        return this.$store.state.todayData.orders_total_formatted;
      },
      isFetchingData: function () {
        return this.$store.state.isFetchingData;
      }
    },

    watch: {
      isFetchingData: function (newValue) {
        if (newValue) {
          this.setWaitOverlay();
        } else {
          this.unsetWaitOverlay();
        }
      }
    },

    methods: {
      setWaitOverlay: function () {
        $(this.$el).closest('.daily-sales-statistic').addClass('reloading reloading-circles');
      },

      unsetWaitOverlay: function () {
        $(this.$el).closest('.daily-sales-statistic').removeClass('reloading reloading-circles');
      }
    }
  });
});
