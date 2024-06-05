/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
  'order-shipping-status-backorder',
  'select.order-shipping-status',
  function () {
    var backorderId = xcart.getCommentedData(this, 'backorder_id');
    var popupContent = xcart.getCommentedData(this, 'popup_content');

    if (backorderId) {
      var self = $(this);
      self.data('opened', false);
      self.data('old_value', self.val());

      xcart.bind('confirmBackorderChanges', function () {
        if (self.data('opened')) {
          self.data('opened', false);
          popup.close();
          $('option[value="' + backorderId + '"]', self).attr('disabled', true);
        }
      });

      xcart.bind('discardBackorderChanges', function () {
        if (self.data('opened')) {
          self.data('opened');
          popup.close();
          self.val(backorderId).change();
        }
      });

      self.change(function () {
        if (String(self.data('old_value')) === String(backorderId)) {
          self.data('opened', true);
          popup.open(
            popupContent,
            {
              closeOnEscape: false,
              dialogClass: 'no-close'
            }
          );
        }
        self.data('old_value', $(this).val());
      });
    }
  }
);
