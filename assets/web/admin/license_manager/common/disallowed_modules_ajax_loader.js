/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Disallowed modules
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/disallowedModules', ['common/coreLicense'], coreLicense => {
  if (!coreLicense.isTrial || (coreLicense.isTrial && coreLicense.isExpired)) {
    const url = xliteConfig.base_url;

    return xcart.get(
      url + 'service.php/api/modules/disallowed',
      null,
      null,
      {
        dataType: 'json'
      });
  }
});
