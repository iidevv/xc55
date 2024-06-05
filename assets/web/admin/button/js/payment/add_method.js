/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add payment method JS controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonAddPaymentMethod()
{
  PopupButtonAddPaymentMethod.superclass.constructor.apply(this, arguments);

  const params = jQuery.getQueryParameters();
  if (params.show_add_payment_popup) {
    jQuery('button.add-online-method-button').click();
  }
}

// New POPUP button widget extends POPUP button class
extend(PopupButtonAddPaymentMethod, PopupButton);

// New pattern is defined
PopupButtonAddPaymentMethod.prototype.pattern = '.add-payment-method-button';

decorate(
  'PopupButtonAddPaymentMethod',
  'callback',
  function (selector)
  {
    jQuery(function () {
      var isChrome = /Chrome\//.test(navigator.userAgent);

      if (isChrome) {
        jQuery('.chosen-search input').attr('autocomplete', 'disable');
      }
    });

    xcart.autoload(TableItemsListQueue);
    SearchConditionBox();
  }
);

decorate(
  'PopupButtonAddPaymentMethod',
  'eachClick',
  function (elem)
  {
    if (jQuery('.ajax-container-loadable.widget-payment-addmethod').length) {
      jQuery('.ajax-container-loadable.widget-payment-addmethod')
        .closest('.ui-widget-content')
        .remove();
    }

    arguments.callee.previousMethod.apply(this, arguments);
  }
);

// Autoloading new POPUP widget
xcart.autoload(PopupButtonAddPaymentMethod);
