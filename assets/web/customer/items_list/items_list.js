/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Main class
function ListsController(base)
{
  ListsController.superclass.constructor.apply(this, arguments);

  if (this.base && this.base.length) {
    this.block = this.getListView();
  }
}

extend(ListsController, AController);

ListsController.prototype.name = 'ItemsListController';

ListsController.prototype.findPattern = '.items-list';

ListsController.prototype.block = null;

ListsController.prototype.selfUpdated = false;

ListsController.prototype.getListView = function()
{
  return new ListView(this.base);
};

// Initialize controller
ListsController.prototype.initialize = function()
{
  var o = this;

  this.base.bind(
    'reload',
    function(event, box) {
      o.bind(box);
    }
  );
};

/**
 * Main widget (listView)
 */

function ListView(base)
{
  this.widgetClass  = xcart.getCommentedData(base, 'widget_class');
  this.widgetTarget = xcart.getCommentedData(base, 'widget_target');
  this.widgetParams = xcart.getCommentedData(base, 'widget_params');
  this.replaceState = xcart.getCommentedData(base, 'replaceState');
  this.replaceStatePrefix = xcart.getCommentedData(base, 'replaceStatePrefix');
  this.listenToGetParams = xcart.getCommentedData(base, 'listenToGetParams');

  ListView.superclass.constructor.apply(this, arguments);
}

extend(ListView, ALoadable);

ListView.prototype.getCurrentPageIdParam = function() {
  const url = new URL(window.location);
  const urlParams = url.searchParams;

  return urlParams.has('pageId')
    ? { 'pageId': parseInt(urlParams.get('pageId'), 10) }
    : {};
};

ListView.prototype.load = function(params)
{
  if (this.replaceState) {
    this.doReplaceState(params);
  }

  if (this.listenToGetParams) {
    const url = new URL(window.location);
    const urlParams = url.searchParams;

    _.mapObject(params, (value, key) => {
      if (key === 'pageId') {
        urlParams.set(key, value);
      }

      if (key !== 'pageId' && key !== 'displayMode') {
        urlParams.delete('pageId');
      }
    });


    url.searchParams = urlParams.toString();

    history.pushState({}, null, url.toString());
  }

  if (!xcart.isRequesterEnabled) {
    return false;
  }

  if (this.isLoading) {
    this.deferredLoad = true;
    return true;
  }

  this.isLoading = true;
  this.deferredLoad = false;

  this.lastLoadParams = params;

  const newUrl = this.buildWidgetRequestURL(params);

  if (this.base) {
    this.base.trigger('preload', [this, newUrl]);

    if (typeof(this.preloadHandler) == 'function') {
      // Call preload event handler
      this.preloadHandler.call(this.base);
    }
  }

  var state = { 'widget': this, 'url': newUrl, 'options': this.getLoaderOptions() }

  this.triggerVent('preload', state);

  this.saveState();

  this.shade();

  state.xhr = xcart.get(state.url, this.loadHandlerCallback, null, state.options);

  this.triggerVent('afterload', state);

  return state.xhr;
};


ListView.prototype.shadeWidget = true;

ListView.prototype.sessionCell = null;

ListView.prototype.postprocess = function(isSuccess, initial)
{
  ListView.superclass.postprocess.apply(this, arguments);

  if (isSuccess) {

    const o = this;

    // Register page click handler
    jQuery('.pagination a', this.base).click(
      function() {
        // scroll page to list top
        jQuery('html, body').animate({scrollTop: o.base.offset().top});

        return !o.load({'pageId': xcart.getValueFromClass(this, 'page')});
      }
    );
    jQuery('input.page-length').validationEngine();

    // Register page count change handler
    jQuery('input.page-length', this.base).change(
      function() {
        if (!o.isLoading) {
          if(!jQuery(this).validationEngine('validate')) {
            count = parseInt(jQuery(this).val());

            if (isNaN(count)) {
              //TODO We must take it from the previous widget parameters ...
              count = 10;

            } else if (count < 1) {
              count = 1;
            }

            if (count != jQuery(this).val()) {
              jQuery(this).val(count);
            }

            o.load({
              'pageId': 1,
              'itemsPerPage': count
            });
          }
        }
      }
    );

    jQuery('input.page-length', this.base).keypress(
      function(event) {
        if (event.keyCode == 13) {
          jQuery(event.currentTarget).change();
        }
      }
    );

    window.addEventListener('popstate', function () {
      if (xcart.getCommentedData(o.base, 'listenToGetParams')) {
        location.reload();
      }
    });
  }
};
