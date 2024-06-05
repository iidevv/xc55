/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Core license ajax loader
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/coreLicense', [], () => {
  const url = xliteConfig.base_url;

  return xcart.get(
    url + 'service.php/api/licenses/core',
    null,
    null,
    {
      dataType: 'json',
      success: data => {
        jQuery('.core-edition-name').html(data.edition);
      }
    }
  );
});
