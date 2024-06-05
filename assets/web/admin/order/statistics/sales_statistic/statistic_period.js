/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Daily sales statistic component (for dashboard)
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('statistic/period', ['js/vue/vue', 'statistic/store'], function (XLiteVue, Store) {
  XLiteVue.component('xlite-period-sales-statistic', {
    store: Store,

    data: function() {
      return {
        revenueColor: '#2274a6',
        ordersColor: '#66b36b',
        chartObj: null,
        url: {
          target: 'sales_statistic',
          action: 'get_period_statistic'
        }
      }
    },

    mounted: function () {
      google.charts.load('current', {'packages':['corechart']});

      google.charts.setOnLoadCallback(_.bind(function () {
        this.chartObj = new google.visualization.AreaChart(this.getContainerElem().find('.sales-chart-container')[0]);
        google.visualization.events.addListener(this.chartObj, 'ready', _.bind(this.chartReadyHandler, this));
      }, this));

      this.getContainerElem()
        .find('.period-sales-selector select')
        .change(_.bind(function (e) {
          var elem = $(e.currentTarget);
          var selectedPeriod = elem.val();

          this.fetchData(selectedPeriod);
        }, this)).change();
    },

    computed: {
      periodData: function () {
        return this.$store.state.periodData;
      },
      periodYoY: function () {
        return this.$store.state.periodYoY;
      },
      periodRevenue: function () {
        return this.$store.state.periodRevenue;
      },
      periodOrders: function () {
        return this.$store.state.periodOrders;
      },
      icuDateFormat: function () {
        return this.$store.state.icuDateFormat;
      },
      isFetchingData: function () {
        return this.$store.state.isFetchingData;
      },
      yoyValueClass: function () {
        switch (true) {
          case this.periodYoY === null:
            return 'yoy-not-available';
          case this.periodYoY > 0:
            return 'yoy-positive';
          case this.periodYoY < 0:
            return 'yoy-negative';
          case this.periodYoY === 0:
            return 'yoy-neutral';
        }
      },
      periodYoYFormatted: function () {
        return Math.abs(this.periodYoY) + '%';
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

    methods: _.extend(
      Vuex.mapActions([
        'updateData',
        'setIsFetchingData'
      ]), {

      setWaitOverlay: function () {
        this.getContainerElem().addClass('reloading reloading-circles');
      },
      unsetWaitOverlay: function () {
        this.getContainerElem().removeClass('reloading reloading-circles');
      },
      fetchData: function (period) {
        this.setIsFetchingData(true);

        var url = this.url;
        url.period = period;

        xcart.post(
          url
        )
          .done(_.bind(function (data) {
            this.updateData(data);
            this.drawChart();
          }, this))
          .always(_.bind(function () {
            this.setIsFetchingData(false);
          }, this));
      },
      getContainerElem: function () {
        return $(this.$el).closest('.period-sales-statistic');
      },
      drawChart: function () {
        if (this.chartObj) {
          this.chartObj.clearChart();
          this.chartObj.draw(this.assembleChartData(), this.assembleChartOptions());
        }
      },
      chartReadyHandler: function () {
        var svg = jQuery('.period-sales-chart .sales-chart-container svg');

        svg.find('path[stroke="none"][stroke-width="0"][fill="' + this.revenueColor + '"]')
          .attr('fill', 'url(#revenue_gradient)');
        svg.find('path[stroke="none"][stroke-width="0"][fill="' + this.ordersColor + '"]')
          .attr('fill', 'url(#orders_gradient)');
      },
      assembleChartData: function () {
        var data = new google.visualization.DataTable();

        data.addColumn('date');
        data.addColumn('number', xcart.t('Revenue'));
        data.addColumn('number', xcart.t('Orders'));

        var rows = [];
        this.periodData.forEach(function (data) {
          var row = [
            new Date(parseInt(data.group_timestamp) * 1000),
            {
              v: parseFloat(data.orders_total),
              f: data.orders_total_formatted
            },
            data.orders_count
          ]

          rows.push(row);
        });

        data.addRows(rows);

        var dateFormatter = new google.visualization.DateFormat({ pattern: this.icuDateFormat });
        dateFormatter.format(data, 0);

        return data;
      },
      assembleChartOptions: function () {
        return {
          chartArea: {
            top: 0,
            height: '85%',
            width: '100%'
          },
          vAxis: { minValue: 0 },
          crosshair: { trigger: 'both', orientation: 'vertical' },
          focusTarget: 'category',
          legend: { position: 'none' },
          pointsVisible: false,
          intervals: { style: 'boxes' },
          pointSize: 1,
          areaOpacity: 1,
          height: 120,
          tooltip: {
            showColorCode: false,
            textStyle: {
              color: '#333',
              fontSize: 12
            }
          },
          hAxis: {
            format: 'MMM d',
            ticks: [],
            allowContainerBoundaryTextCutoff: true,
            gridlines: {
              count: 0,
              color: 'transparent'
            },
            textStyle: {
              color: '#cccccc',
              fontSize: 10,
            }
          },
          series: [
            { color: this.revenueColor, targetAxisIndex: 0, lineWidth: 1 },
            { color: this.ordersColor, targetAxisIndex: 1, lineWidth: 0.3 }
          ],
          vAxes: [
            {
              textPosition: 'none',
              baselineColor: '#f5f5f5',
              gridlines: {
                count: 0
              }
            },
            {
              textPosition: 'none',
              baselineColor: '#f5f5f5',
              gridlines: {
                count: 0
              }
            }
          ]
        };
      }
    })
  });
});
