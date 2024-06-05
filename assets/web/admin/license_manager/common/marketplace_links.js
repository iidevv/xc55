/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Marketplace links controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.bind(['load', 'popup.postprocess', 'list.model.table.initialize'], function () {
  define('setShopIdentifierToLinks', ['common/coreLicense', 'js/jquery'], (coreLicense, $) => {
    coreLicense.then(license => {
      $('a.marketplace-link').each(function () {
        const url = new URL($(this).prop('href'));
        url.searchParams.set('xc5_shop_identifier', license.shopIdentifier);
        $(this).prop('href', url.href);
      });
    })
  });
});
