/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Shipping items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Main class
function ItemsListShipping(elem)
{
  this.base = elem;

  this.addListeners();
}

ItemsListShipping.prototype.addListeners = function ()
{
  const base = this.base;

  jQuery('.switcher input[type=checkbox]', this.base).change(function (event)
  {
    event.stopImmediatePropagation();

    xcart.trigger('shipping.methods.switch', jQuery(this));

    return false;
  });

  jQuery('.remove a', this.base).click(function (event)
  {
    event.stopImmediatePropagation();

    if (!confirm(xcart.t('Are you sure you want to delete the selected shipping method?'))) {
      return false;
    }

    xcart.trigger(
      'shipping.methods.remove',
      {
        href: jQuery(this).attr('href'),
        line: jQuery(this).closest('.cell'),
        base: base
      }
    );

    return false;
  });
};

// Shipping methods remove event
xcart.bind(
  'shipping.methods.remove',
  function (event, data)
  {
    const line = data.line;
    const base = data.base;
    const href = data.href;

    xcart.get(
      href,
      function () {
        xcart.trigger('shipping.methods.remove.loaded', {line: line, base: base});
      }
    );
  }
);

// Shipping methods remove loaded event
xcart.bind(
  'shipping.methods.remove.loaded',
  function (event, data)
  {
    event.stopImmediatePropagation();
    data.line.remove();

    if (data.base.find('li.cell').length === 0) {
      data.base.closest('.dialog-content').remove();
    }
  }
);

xcart.microhandlers.add(
  'ItemsListShippingQueue',
  '.items-list.methods',
  function (event) {
    jQuery(this).each(function (index, elem) {
      new ItemsListShipping(jQuery(elem));
    });
  }
);
