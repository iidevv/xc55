/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add address button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
    'Tooltipped init',
    '.with-tooltip',
    function () {
      jQuery(this).tooltip();
    }
);
