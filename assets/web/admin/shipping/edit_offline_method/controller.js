/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Edit offline shipping method controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
  'offlineHelp',
  '.offline-help .help-link',
  function () {
    jQuery(this).on('click', function () {
      jQuery('.offline-help .help-content').toggle();
    });
  }
);
