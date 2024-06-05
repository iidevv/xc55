/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('datepicker', {
    inserted: function (el, binding) {
      var $el = $(el);
      var model = binding.expression.split('.');
      var prop = model.pop();
      var defaultDate = $el.val();
      var vm = Vue.getClosestVueInstance(el);

      if (!vm) {
        return;
      }

      var target = _.get(vm, model);

      var format = el.getAttribute('format')
        , firstday = el.getAttribute('firstday')
        , locale = el.getAttribute('dictionary');

      var changeHiddenValue = function ($el) {
        var selectedDate = $.datepicker.formatDate($($el).datepicker('option', 'dateFormat'), $($el).datepicker('getDate'), $.datepicker.regional['']);
        $($el).siblings('.datepicker-value-input').val(selectedDate);
      };

      $.datepicker.setDefaults($.datepicker.regional['']);
      if ($.datepicker.regional[locale] !== undefined) {
        $.datepicker.setDefaults($.datepicker.regional[locale]);

        if (defaultDate !== undefined && defaultDate !== '') {
          defaultDate = $.datepicker.parseDate(format, defaultDate, $.datepicker.regional['']);
        }
      }

      $el.datepicker({
        dateFormat: format,
        defaultDate: defaultDate,
        firstDay: parseInt(firstday),
        onSelect: function (date) {
          changeHiddenValue(this);

          Vue.set(target, prop, '' + $(this).datepicker('getDate'));
        }
      });

      $el.change(function(){
        changeHiddenValue(this);

        Vue.set(target, prop, '' + $(this).datepicker('getDate'));
      });

      $el.blur(function () {
        var result = null;
        try {
          result = $.datepicker.parseDate(format, $el.val());
        } catch(err) {
          result = false;
        }

        if (!result) {
          $el.datepicker('setDate', defaultDate);
          $el.datepicker('refresh');

          changeHiddenValue($el);
        }
      });

      $el.datepicker('setDate', defaultDate);
      $el.datepicker('refresh');

      changeHiddenValue($el);
    }
  });

})();
