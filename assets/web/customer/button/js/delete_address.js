/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Delete address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ButtonDeleteAddress()
{
  var obj = this;

  jQuery('.delete-address').each(
    function () {
      var o = jQuery(this);
      var addressId = xcart.getCommentedData(o, 'address_id');
      var warningText = xcart.getCommentedData(o, 'warning_text');

      o.click(function () {
        result = confirm(warningText);
        if (result) {
          xcart.post(obj.getParams(addressId), function () {
            o.closest('li.address-book').remove();
          });
        }
      });
    }
  );
}

ButtonDeleteAddress.prototype.getParams = function (addressId)
{
  var result = {
    'address_id'  : addressId,
    'target'      : 'address_book',
    'action'      : 'delete'
  };
  result[xliteConfig.form_id_name] = xliteConfig.form_id;

  return result;
};

xcart.autoload(ButtonDeleteAddress);
