/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Edit shipping method JS controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonEditShippingMethod()
{
  PopupButtonEditShippingMethod.superclass.constructor.apply(this, arguments);

  xcart.bind('list.model.table.initialize', function() {
    xcart.autoload(PopupButtonEditShippingMethod);
  });
}

extend(PopupButtonEditShippingMethod, PopupButton);

PopupButtonEditShippingMethod.prototype.pattern = '.edit-shipping-method-button';

decorate(
    'PopupButtonEditShippingMethod',
    'eachClick',
    function (elem)
    {
      if (jQuery('.ajax-container-loadable.widget-shipping-editmethod').length) {
        jQuery('.ajax-container-loadable.widget-shipping-editmethod').closest('.ui-widget-content').remove();
      }

      jQuery(elem).toggleClass('always-reload', elem.linkedDialog && jQuery(elem.linkedDialog).length === 0);

      arguments.callee.previousMethod.apply(this, arguments);
    }
);

decorate(
  'PopupButtonEditShippingMethod',
  'callback',
  function ()
  {
    xcart.microhandlers.add(
      'ItemsListMarkups',
      '.offline-shipping-create',
      function () {
        xcart.autoload(TableItemsListQueue);
      }
    )
  }
);

xcart.autoload(PopupButtonEditShippingMethod);
