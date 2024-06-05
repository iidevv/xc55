/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product selection button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonProductSelector(base)
{
  PopupButtonProductSelector.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonProductSelector, PopupButton);

PopupButtonProductSelector.prototype.pattern = '.popup-product-selection';

decorate(
  'PopupButtonProductSelector',
  'callback',
  function (selector)
  {
    // Some autoloading could be added
    xcart.autoload(TableItemsListQueue);
    xcart.autoload(CommonForm);
    xcart.autoload(StickyPanelProductSelection);
    SearchConditionBox();
  }
);

decorate(
  'PopupButtonProductSelector',
  'getURLParams',
  function ()
  {
    var params = arguments.callee.previousMethod.apply(this, arguments);

    return params;
  }
);

xcart.autoload(PopupButtonProductSelector);

xcart.microhandlers.add(
  'PopupButtonProductSelector',
  PopupButtonProductSelector.prototype.pattern,
  function (event) {
    xcart.autoload(PopupButtonProductSelector);
  }
);
