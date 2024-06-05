/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Search pabel functionality
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function SearchFiltersView(base)
{
  ALoadable.apply(this, arguments);
}

extend(SearchFiltersView, ALoadable);

SearchFiltersView.autoload = function()
{
  new SearchFiltersView(jQuery('.search-widget-container'));
}

SearchFiltersView.prototype.postprocess = function(isSuccess, initial)
{
  SearchFiltersView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {
    const self = this;
    const widget = self.base.get(0) ? self.base.get(0).dataset.widget : null;
    const loadFilter = (link, removeFilter = false) => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const params = new URL(link.href).searchParams;

        const paramsObj = _.reduce(Array.from(params), (a, b) => {
          return { ...a, ...{ [b[0]]: b[1] } };
        }, {});

        if (removeFilter) {
          const confirmDeleteFilter = confirm(xcart.t('Are you sure you want to delete this filter?'))

          if (!confirmDeleteFilter) {
            return false;
          }

          xcart.get({
            target: paramsObj.target,
            action: 'clearSearch',
            xcart_form_id: paramsObj.xcart_form_id
          })
        }

        xcart.get(paramsObj).done(function () {
          !self.load({
            target: paramsObj.target,
            widget
          });
        });
      });
    }

    const filters = document.querySelectorAll('.filter-title');

    if (filters) {
      filters.forEach((item) => loadFilter(item));
    }

    const deleteFilters = document.querySelectorAll('.delete-filter');

    if (deleteFilters) {
      deleteFilters.forEach((item) => loadFilter(item, true));
    }

    const clearSearch = document.querySelector('.search-conditions-actions .clear-search');

    if (clearSearch) {
      loadFilter(clearSearch);
    }

    const saveFilter = document.querySelector('.save-search-filter button');

    if (saveFilter) {
      saveFilter.addEventListener('click', () => xcart.get({
        target: xliteConfig.target,
        action: 'save_search_filter'
      }).done(function () {
        return !self.load({
          target: xliteConfig.target,
          widget
        });
      }));
    }

    if (!_.isUndefined(StatesList)) {
      StatesList.getInstance().updateStatesList();
    }

  }
}

xcart.bind('loader.loaded', function () {
  ItemsListQueue();

  if (xliteConfig.role === 'vendor') {
    SearchConditionBox();
  }
});

xcart.autoload(SearchFiltersView);

var searchCallback = function ($form, linked) {
  if (_.isUndefined(jQuery(linked).get(0))) {
    return;
  }

  var $linked = jQuery(linked).get(0).itemsListController;
  $linked.cleanURLParams();
  $linked.reinitializeUrlParamsByCommentedData();

  $form.find(':input').not('button').each(function (id, elem) {
    if ('action' !== jQuery(elem).attr('name') && 'returnURL' !== jQuery(elem).attr('name')) {

      var value = jQuery(elem).val();
      var skipParam = false;

      if (value === null) {
        value = '';
      }

      if (
        'checkbox' === jQuery(elem).attr('type')
        && false == jQuery(elem).prop('checked')
      ) {
        value = '';
      }

      if (
        'radio' === jQuery(elem).attr('type')
        && false == jQuery(elem).prop('checked')
      ) {
        skipParam = true;
      }

      if (!skipParam) {
        if (jQuery.isArray(value) && value.length) {
          for (var x in value) {
            $linked.setURLParam(jQuery(elem).attr('name').replace('[]', '[' + x + ']'), value[x]);
          }

        } else {
          $linked.setURLParam(jQuery(elem).attr('name'), value);
        }
      }
    }
  });

  $linked.loadWidget(function (content){
    var newFormId = jQuery('.search-conditions-box', content).closest('form').find('input[name="xcart_form_id"]').val();
    $form.find('input[name="xcart_form_id"]').val(newFormId);
  });
};

var SearchConditionBox = function (submitFormFlag)
{
  var makeSubmitFormFlag = !_.isUndefined(submitFormFlag) && (submitFormFlag === true);
  const clearFiltersButton = jQuery('.saved-filter-options .clear-filter');

  // Switch secondary box visibility
  jQuery('.search-conditions-box .arrow').click(
    function () {
      var searchConditions = jQuery('.search-conditions-box');
      if (searchConditions.hasClass('full')) {
        searchConditions.removeClass('full')
      } else {
        searchConditions.addClass('full')
      }
    }
  );

  // Add some additional functionality for the search conditions boxes
  jQuery('.search-conditions-box').each(
    function () {
      const $this = jQuery(this);
      const linked = xcart.getCommentedData($this, 'linked');
      const actionsBox = jQuery('.actions-bottom', $this);

      if (jQuery(linked).length > 0) {
        var $form = $this.parents('form').eq(0);

        $form.on('change', function (event) {
          if (
            !jQuery(event.target).closest('.button-action').length
            && jQuery(event.target).prop('nodeName') !== 'FORM'
            && !(
              jQuery(event.target).prop('nodeName') === 'SELECT'
              && jQuery(event.target).val() === ''
            )
          ) {
            actionsBox.removeClass('disabled').addClass('disabled-save');
          }
        }).submit(
          function (event) {
            event.preventDefault();

            var formAction = jQuery('input[name="action"]', $form).eq(0).val();
            clearFiltersButton.removeClass('active');

            jQuery.ajax({
              type:   $form.attr('method'),
              url:    $form.attr('action'),
              data:   $form.serialize(),
              success: function (data, status, xhr)
              {
                if (xhr.status === 200) {
                  actionsBox.removeClass('disabled disabled-save');
                }

                if (formAction == 'search' || formAction == 'searchItemsList') {
                  searchCallback($form, linked);

                } else if (formAction == 'save_search_filter') {
                  if (xhr.getResponseHeader('event-messages')) {
                    var filterForm = $($form).find('.save-search-filter');
                    var inputs = $($form).find(':input');

                    var eventData = xhr.getResponseHeader('event-messages');
                    var eventMsg = JSON.parse(eventData);

                    Object.keys(eventMsg).forEach(function(key) {
                      xcart.trigger('message', {type: eventMsg[key].type, message: eventMsg[key].message});
                      if (eventMsg[key].type === 'error') {
                        filterForm.addClass('has-error');

                        if (inputs.length) {
                          _.each(inputs, function(input) {
                            $(input).attr('disabled', false);
                          });
                        }
                      }
                    });
                  }
                } else {
                  location.reload();
                }
              }
            });

            return false;
          }
        );

        if (makeSubmitFormFlag) {
          $form.submit();
        }
      }
    }
  );

  // Expand secondary box if box has filled fields
  var boxes = jQuery('.search-conditions-box:not(.full) .search-conditions-hidden');
  if (boxes.length) {
    boxes.each(
      function() {
        var filled = false;
        var parentBlock = jQuery(this).parents('.search-conditions-box').eq(0);
        if (0 < parentBlock.length && true != xcart.getCommentedData(parentBlock, 'hideAdditionalFields')) {
          jQuery(this).find('input[type="text"],input[type="radio"]:checked:not(.default),input[type="checkbox"]:checked,select,textarea').each(
            function() {
              if (jQuery(this).val()) {
                if (jQuery(this).attr('id') == 'stateSelectorId') {
                  if (
                    jQuery(this).data('value') != ''
                    && jQuery('#country').val()
                    && StatesList.getInstance().getStates(jQuery('#country').val())
                  ) {
                    filled = true;
                  }
                } else {
                  filled = true;
                }
              }
            }
          );

          if (filled) {
            parentBlock.addClass('full');
            clearFiltersButton.removeClass('active');
          }
        }
      }
    );
  }
};

jQuery().ready(SearchConditionBox);
