/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product details controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
  'assignPopupHandlersOnNotifications',
  '.infoblock-notifications',
  function() {
    xcart.autoload(PopupButton);
  }
);
