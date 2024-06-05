/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product selection button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupAddCategoryProduct(base)
{
  PopupAddCategoryProduct.superclass.constructor.apply(this, arguments);
}

extend(PopupAddCategoryProduct, PopupButton);

PopupAddCategoryProduct.prototype.pattern = '.popup-product-category-selection';

decorate(
  'PopupAddCategoryProduct',
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
  'PopupAddCategoryProduct',
  'getURLParams',
  function ()
  {
    var params = arguments.callee.previousMethod.apply(this, arguments);

    return params;
  }
);

xcart.autoload(PopupAddCategoryProduct);

xcart.microhandlers.add(
  'PopupAddCategoryProduct',
  PopupAddCategoryProduct.prototype.pattern,
  function (event) {
    xcart.autoload(PopupAddCategoryProduct);
  }
);
