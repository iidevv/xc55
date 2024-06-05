/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xliteAutocomplete', {
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
        , noResultsLbl = el.getAttribute('no-results-lbl')
        , placeholderLbl = el.getAttribute('placeholder-lbl')
        , shortLbl = el.getAttribute('short-lbl')
        , moreLbl = el.getAttribute('more-lbl')
        , dictionary = el.getAttribute('dictionary');

      $el
        .select2({
          debug: xcart.isDeveloperMode,
          language: {
            noResults: function () {
              return noResultsLbl;
            },
            searching: function () {
              return '<span class="searching">' + searchingLbl + '</span>';
            },
            inputTooShort: function () {
              return shortLbl;
            },
            loadingMore: function () {
              return '<span class="loading-more">' + moreLbl + '</span>';
            }
          },
          minimumInputLength: 3,
          placeholder: placeholderLbl,
          escapeMarkup: function (markup) { return markup; },
          ajax: {
            url: xliteConfig.script + "?target=autocomplete",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              var query = {
                term: params.term,
                dictionary: dictionary
              };

              return query;
            },
            processResults: function (data) {
              $.each(data, function (index, value) {
                data[index].id = value.value;
                data[index].text = value.label.name;
              });

              return {
                results: data
              };
            },
          },
          templateResult: function (item, selectItem) {
            if (item.loading) {
              return '<span class="searching">' + searchingLbl + '</span>';
            }

            var markup = '<span>' + item.label.name + '</span>';

            $(selectItem).data('name', item.label.name);

            return markup;
          },
        })
        .on('select2:select', function () {
          Vue.set(target, prop, $el.val() || []);
        })
        .on('select2:unselect', function () {
          Vue.set(target, prop, $el.val() || []);
        });

      $el.trigger('select2:select');
    },

    update: function (el, binding) {
      var $el = $(el);

      $el.val(binding.value);
      $el.trigger('change');
    }
  });
})();
