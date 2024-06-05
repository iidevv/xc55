/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteSelect2', {
    inserted: function (el, binding) {
      var $el = $(el);
      var model = binding.expression.split('.');
      var prop = model.pop();
      var vm = Vue.getClosestVueInstance(el);

      if (!vm) {
        return;
      }

      var target = _.get(vm, model);

      var searchingLbl = el.getAttribute('searching-lbl')
        , noResultsLbl = el.getAttribute('no-results-lbl');

      $el
        .select2(
          {
            language: {
              noResults: function () {
                return noResultsLbl;
              },
              searching: function () {
                return searchingLbl;
              }
            },
            escapeMarkup: function (markup) {
              return markup;
            }
          }
        )
        .on('select2:select', function () {
          Vue.set(target, prop, $el.val() || []);
        })
        .on('select2:unselect', function () {
          Vue.set(target, prop, $el.val() || []);
        });
    }
  });

})();
