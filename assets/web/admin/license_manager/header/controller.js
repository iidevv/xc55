/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Header license notice
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('header/licenseNotice', ['common/coreLicense'], coreLicense => {
  const noticeBlock        = jQuery('.header-trial-notice'),
        messageContainer   = noticeBlock.find('.message'),
        currentTimestamp   = Math.floor(Date.now() / 1000),
        remainingTrialDays = coreLicense.expiredAt > currentTimestamp
          ? Math.ceil((coreLicense.expiredAt - currentTimestamp) / 86400)
          : 0,
        messageToShow      = remainingTrialDays
          ? xcart.t('Your X-Cart trial expires in X day(s)', {count: remainingTrialDays})
          : xcart.t('Trial has expired!');

  if (coreLicense.isTrial) {
    messageContainer.html(messageToShow);
    noticeBlock.removeClass('hide');

    jQuery('#leftMenu').css({
      top: noticeBlock.height()
    });
  }
});
