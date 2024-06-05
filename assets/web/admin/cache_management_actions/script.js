/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
  'rebuild-btn-click',
  '.rebuild-btn',
  function () {
    jQuery(this).bind('click', function () {
      if (confirm(xcart.t('Are you sure?'))) {
        const rebuild = new Rebuild({});
        rebuild.run();
      }
    });
  }
);