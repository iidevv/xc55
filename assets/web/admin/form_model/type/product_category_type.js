/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  function drawTooltipOnLongTitles(elements) {
    elements
      .filter(function() {
        return $(this).attr('title').length > 40
      })
      .tooltip({
        placement: 'auto bottom'
      });
  }

  function addDisabledCategoryTooltip(optionText, tooltipLbl, element, rootSelect, enabled) {
    var option = $(rootSelect[0].el)
      .find('option')
      .filter(function(index, element) {
        return element.text.endsWith(optionText);
      });

    if ((option.length > 0 && option.data('disabled')) || !enabled) {
      element
        .tooltip({
          title: tooltipLbl,
          html: true
        })
        .attr('data-disabled', true);
    }
  }

  function markInaccessibleOptions(elements, rootSelect) {
    elements.each(function() {
      var text = $(this).attr('title') || $(this).attr('data-original-title');
      var option = $(rootSelect[0].el)
        .find('option')
        .filter(function(index, element) {
          return element.text.endsWith(text);
        });

      if (option.length > 0 && option.data('disabled')) {
        $(this).attr('data-disabled', true);
      }
    })
  }

  Vue.directive('xliteProductCategory', {
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
        , placeholderLbl = ''
        , disabledLbl = el.getAttribute('disabled-lbl')
        , shortLbl = el.getAttribute('short-lbl')
        , moreLbl = el.getAttribute('more-lbl');

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
            inputTooShort:function () {
              return shortLbl;
            },
            loadingMore:function () {
              return '<span class="loading-more">' + moreLbl + '</span>';
            }
          },
          minimumInputLength: 3,
          placeholder: placeholderLbl,
          escapeMarkup: function (markup) { return markup; },
          ajax: {
            url: xliteConfig.script + "?target=search_categories",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              var query = {
                search: params.term,
                page: params.page || 1
              };

              return query;
            },
            processResults: function (data, params) {
              params.page = params.page || 1;

              return {
                results: data.categories,
                pagination: {
                  more: data.more
                }
              };
            },
          },
          templateResult: function (category, selectItem) {
            if (category.loading) {
              return '<span class="searching">' + searchingLbl + '</span>';
            }

            var parts = category.path.split('/').map(function (item) {
              return xcart.utils.escapeString(item);
            });

            var markup = '';
            if (parts.length > 1) {
              markup += '<span class="path">' + parts.slice(0, -1).join(' / ') + ' / </span>';
            }

            var name = xcart.utils.escapeString(category.name)
            markup += '<span class="name">' + name + '</span>';

            $(selectItem).data('name', name);

            if (category.enabled === undefined) {
              category.enabled = true;
            }

            addDisabledCategoryTooltip(name, disabledLbl, $(selectItem), $el, category.enabled);

            return markup;
          },
          templateSelection: function (category, selectItem) {
            var path = category.path === undefined ? category.text : category.path;
            var parts = path.split('/').map(function (item) {
              return xcart.utils.escapeString(item);
            });

            var tooltipText = '<div class="path">';
            parts.forEach(function(part) {
              tooltipText += '<div class="part">' + part;
            });
            parts.forEach(function() {
              tooltipText += '</div>';
            });
            tooltipText += '</div>';

            $(selectItem)
              .data('data', category)
              .tooltip({
                title: tooltipText,
                html: true,
                placement: 'auto bottom'
              });

            var name = category.name ? xcart.utils.escapeString(category.name) : parts.pop();

            if (category.enabled === undefined) {
              category.enabled = true;
            }

            addDisabledCategoryTooltip(name, disabledLbl, $(selectItem), $el, category.enabled);
            $('.tooltip').hide();

            return name;
          }
        })
        .on('select2:select', function () {
          target[prop] = $el.val() || [];
        })
        .on('select2:unselect', function () {
          target[prop] = $el.val() || [];
        })
        .on('change', function () {
          var selectedOptions = $el.parent().find('.select2-selection__choice');
          markInaccessibleOptions(selectedOptions, $el);
          drawTooltipOnLongTitles(selectedOptions);
        });

      var selectedOptions = $el.parent().find('.select2-selection__choice');
      markInaccessibleOptions(selectedOptions, $el);
      drawTooltipOnLongTitles(selectedOptions);

      vm.$watch(binding.expression, function (newValue) {
        vm['form']['default']['category_tree'] = newValue;
      });

      vm.$watch('form.default.category_tree', function (newValue) {
        target[prop] = newValue;
      });

      jQuery(el).select2Sortable(function() {
        var ul = $el.next('.select2-container')
          .first('ul.select2-selection__rendered');

        var reservedChoices = jQuery(ul).find('.select2-selection__choice').get().reverse();

        jQuery(reservedChoices).each(function() {
          var id = $(this).data('data').id;
          var option = $this.find('option[value="' + id + '"]')[0];
          $this.prepend(option);
        });

        vm['form']['default']['category_tree'] = $el.val();
      });

      setTimeout(function () {
        $el.closest('.input-widget').find('span.help-block a').click(function () {
          vm['form']['default']['category_widget_type'] = 'tree';
          jQuery.cookie('product_modify_categroy_widget', 'tree');
        });

        $('#form_default_category_tree').attr('name', '').closest('.input-widget').find('span.help-block a').click(function () {
          vm['form']['default']['category_widget_type'] = 'search';
          jQuery.cookie('product_modify_categroy_widget', 'search');
        });
      }, 1000);
    },
    update: function (el, binding) {
      var newValue = binding.value;
      if (newValue.filter(function (value) { return value.length }).length === 0) {
        return;
      }

      var $el = $(el);

      $el.val(newValue);
      $el.trigger('change');
    }
  });

})();
