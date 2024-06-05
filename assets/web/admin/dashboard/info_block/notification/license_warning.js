/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Dashboard info block license warning controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('sidebar/licenseNotice', ['common/disallowedModules'], disallowedModules => {
  const block   = jQuery('.infoblock-notification.license-warning'),
        counter = block.find('.counter');

  if (disallowedModules && disallowedModules.length > 0) {
    counter.html('(' + disallowedModules.length + ')');
    block.removeClass('hide');
  }
});
