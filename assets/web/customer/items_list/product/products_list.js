/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Products list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ProductsListView(base)
{
  ProductsListView.superclass.constructor.apply(this, arguments);

  xcart.bind('popup.close', _.bind(this.handleCloseQuickLookPopup, this));

  this.requests = [];
}

extend(ProductsListView, ListView);

// Products list class
function ProductsListController(base)
{
  ProductsListController.superclass.constructor.apply(this, arguments);

  xcart.bind('updateCart', _.bind(this.updateCartHandler, this));
}

extend(ProductsListController, ListsController);

ProductsListController.prototype.name = 'ProductsListController';

ProductsListController.prototype.findPattern += '.items-list-products';

ProductsListController.prototype.getListView = function()
{
  return new ProductsListView(this.base);
};

ProductsListController.prototype.updateCartHandler = function(event, data) {
  if (_.isUndefined(data.items)) {
    return;
  }

  for (let i = 0; i < data.items.length; i++) {
    if (data.items[i].object_type == 'product') {

      // Added mark
      const productPattern = '.product.productid-' + data.items[i].object_id;
      const product = jQuery(productPattern, this.base);
      const oldQty = product.find('.qty-in-cart').val();

      if (
        !oldQty
        || !data.items[i].quantity_change
        || !product.hasClass('qty-update-is-needed')
      ) {
        return;
      }

      const newQty = parseInt(oldQty) + data.items[i].quantity_change;

      if (newQty > 0) {
        product.addClass('product-added');
        if (this.block) {
          this.block.triggerVent('item.addedToCart', {'view': this, 'item': product});
        }

      } else {
        product.removeClass('product-added');
        if (this.block) {
          this.block.triggerVent('item.removedFromCart', {'view': this, 'item': product});
        }
      }

      if (product.hasClass('qty-update-is-needed')) {
        product
          .find('.qty-in-cart')
          .val(newQty);

        product.removeClass('qty-update-is-needed');
      }

      // Check inventory limit
      if (data.items[i].is_limit) {
        product.addClass('out-of-stock');

        product
          .find('.increase-qty')
          .prop('disabled', true);

        product
          .find('.add-to-cart')
          .prop('disabled', true);

        if (this.block) {
          this.block.triggerVent('item.outOfStock', {'view': this, 'item': product});
        }

      } else {
        product.removeClass('out-of-stock all-stock-in-cart')

        product
          .find('.increase-qty')
          .prop('disabled', false);

        product
          .find('.add-to-cart')
          .prop('disabled', false);

        if (this.block) {
          this.block.triggerVent('item.stockIncrease', {'view': this, 'item': product});
        }
      }
    }
  }
};

ProductsListView.prototype.postprocess = function(isSuccess, initial)
{
  ProductsListView.superclass.postprocess.apply(this, arguments);

  const self = this;

  if (isSuccess) {

    // Column switcher for 'table' display mode
    jQuery('.products-table .column-switcher', this.base).commonController('markAsColumnSwitcher');

    const currentPageIdParam = self.getCurrentPageIdParam();

    // Register "Changing display mode" handler
    jQuery('.display-modes a', this.base).click(
      function() {
        return !self.load({
          ...currentPageIdParam,
          'displayMode': jQuery(this).attr('class')
        });
      }
    );

    // Register "Sort by" selector handler
    jQuery('.sort-crit a', this.base).click(
      function () {
        return !self.load({
          'pageId': 1,
          'sortBy': jQuery(this).data('sort-by'),
          'sortOrder': jQuery(this).data('sort-order'),
          'mode': 'append'
        });
      }
    );

    xcart.bind(
      'afterPopupPlace',
      function() {
        new ProductDetailsController(jQuery('.ui-dialog div.product-quicklook'));
      }
    );

    // Manual set cell's height
    this.base.find('table.products-grid tr').each(
      function () {
        let height = 0;
        jQuery('div.product', this).each(
          function() {
            height = Math.max(height, jQuery(this).height());
          }
        );
      }
    );

    // Process click on 'Add to cart' buttons by AJAX
    jQuery('.add-to-cart, .increase-qty', this.base).not('.link').each(
      function (index, elem) {
        jQuery(elem).click(function() {
          let product = $(elem).closest('.product-cell').find('.product');
          if (!product.length) {
            product = $(elem).closest('.product-cell');
          }

          const pid = xcart.getValueFromClass(product, 'productid');
          const forceOptions = product.is('.need-choose-options');
          const changeQuantityButtons = self.getChangeQuantityButtons(product);

          if (pid && !self.isLoading) {
            changeQuantityButtons.forEach((button) => {
              button.element.prop('disabled', true);
            });
          }

          product.addClass('qty-update-is-needed');

          if (forceOptions) {
            changeQuantityButtons.forEach((button) => {
              button.element.prop('disabled', button.disabledState);
            });
            self.openQuickLook(product);
          } else {
            xcart.trigger('addToCartViaClick', {productId: pid});

            self.addToCart(elem)
              .always(function() {
                changeQuantityButtons.forEach((button) => {
                  button.element.prop('disabled', button.disabledState);
                });
              });
          }
        });
      }
    );

    // Process click on 'Decrease quantity' button by AJAX
    jQuery('.decrease-qty', this.base).each(
      function (index, elem) {
        jQuery(elem).click(function() {
          let product = $(elem).closest('.product-cell').find('.product');
          if (!product.length) {
            product = $(elem).closest('.product-cell');
          }

          const pid = xcart.getValueFromClass(product, 'productid');
          const changeQuantityButtons = self.getChangeQuantityButtons(product);

          if (pid && !self.isLoading) {
            changeQuantityButtons.forEach((button) => {
              button.element.prop('disabled', true);
            });
          }

          product.addClass('qty-update-is-needed');

          xcart.trigger('removeFromCartViaClick', {productId: pid});

          self.removeFromCart(elem)
            .always(function() {
              changeQuantityButtons.forEach((button) => {
                button.element.prop('disabled', button.disabledState);
              });
            });
        });
      }
    );
  } // if (isSuccess)
}; // ProductsListView.prototype.postprocess()


ProductsListView.prototype.productPattern = '.products-grid .product, .products-list .product, .products-sidebar .product';


// Post AJAX request to add product to cart
ProductsListView.prototype.addToCart = function (elem) {
  const $elem = jQuery(elem);
  const pid = xcart.getValueFromClass($elem, 'productid');

  let xhr = new $.Deferred();

  if (pid && !this.isLoading) {
    if (this) {
      xhr = xcart.post(
        URLHandler.buildURL({ target: 'cart', action: 'add' }),
        _.bind(this.handleAddToCart, this),
        this.addToCartRequestParams($elem),
        {
          rpc: true
        }
      );
    }
  } else {
    xhr.reject();
  }

  return xhr;
};

ProductsListView.prototype.addToCartRequestParams = function ($elem) {
  const pid = xcart.getValueFromClass($elem, 'productid');
  const isProductAdded = ($elem.closest('.product-added').length > 0);

  return {
    target: 'cart',
    action: 'add',
    product_id: pid,
    addLastUpdatedItem: isProductAdded
  }
}

ProductsListView.prototype.handleAddToCart = function (XMLHttpRequest, textStatus, data, isValid) {
  if (!isValid) {
    xcart.trigger(
      'message',
      {
        text: 'An error occurred during adding the product to cart. Please refresh the page and try to add the product to cart again or contact the store administrator.',
        type: 'error'
      }
    );
  }
};

// Post AJAX request to remove product from cart
ProductsListView.prototype.removeFromCart = function (elem) {
  const $elem = jQuery(elem);
  const pid = xcart.getValueFromClass($elem, 'productid');

  let xhr = new $.Deferred();

  if (pid && !this.isLoading) {
    if (this) {
      xhr = xcart.post(
        URLHandler.buildURL({ target: 'cart', action: 'remove' }),
        _.bind(this.handleRemoveFromCart, this),
        this.removeFormCartRequestParams($elem),
        {
          rpc: true
        }
      );
    }
  } else {
    xhr.reject();
  }

  return xhr;
};

ProductsListView.prototype.removeFormCartRequestParams = function (elem) {
  const pid = xcart.getValueFromClass(elem, 'productid');

  return {
    target:     'cart',
    action:     'remove',
    product_id: pid
  }
}

ProductsListView.prototype.handleRemoveFromCart = function (XMLHttpRequest, textStatus, data, isValid) {
  if (!isValid) {
    xcart.trigger(
      'message',
      {
        text: 'An error occurred during removing the product from cart. Please refresh the page and try to remove the product from cart again or contact the store administrator.',
        type: 'error'
      }
    );
  }
};

ProductsListView.prototype.focusOnFirstOption = _.once(function() {
  xcart.bind('afterPopupPlace', function(event, data){
    if (popup.currentPopup.box.hasClass('ctrl-customer-quicklook')) {
      const option = popup.currentPopup.box.find('.editable-attributes select, input').filter(':visible').first();
      option.focus();
    }
  })
});

ProductsListView.prototype.openQuickLook = function(elem) {
  this.focusOnFirstOption();
  return !popup.load(
    URLHandler.buildURL(this.openQuickLookParams(elem)),
    function () {
      jQuery('.formError').hide();
    },
    50000
  );
};

ProductsListView.prototype.handleCloseQuickLookPopup = function(event, data)
{
  if (data.box && data.box.find('.product-quicklook').length > 0 && jQuery('body').find(data.box).length > 0) {
    data.box.dialog('destroy');
    data.box.remove();
  }
};

ProductsListView.prototype.openQuickLookParams = function (elem) {
  const product_id = xcart.getValueFromClass(elem, 'productid');

  return {
    target:      'quick_look',
    action:      '',
    product_id:  product_id,
    only_center: 1
  }
}

// Get event namespace (prefix)
ProductsListView.prototype.getEventNamespace = function () {
  return 'list.products';
};

ProductsListView.prototype.getChangeQuantityButtons = function (product) {
  const result = [];
  const buttons = [
    product.find('.add-to-cart'),
    product.find('.increase-qty'),
    product.find('.decrease-qty')
  ];

  buttons.forEach((button) => {
    result.push({
      'element': button,
      'disabledState': button.prop('disabled')
    })
  });

  return result;
}

/**
 * Load product lists controller
 */
xcart.autoload(ProductsListController);
