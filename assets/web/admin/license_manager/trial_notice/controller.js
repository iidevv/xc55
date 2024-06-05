/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Trial notice controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/trial-notice', ['common/coreLicense', 'js/jquery'], function (coreLicense, $) {
  xcart.microhandlers.add(
    'trialNoticeBlock',
    '.trial-notice-block',
    function () {
      const showOnlyExpired = $(this).hasClass('show-only-expired');
      coreLicense.then(license => {
        if (
          (license.isTrial && !showOnlyExpired)
          || (license.isTrial && license.isExpired && showOnlyExpired)
        ) {
          const currentTimestamp   = Math.floor(Date.now() / 1000),
                remainingTrialDays = license.expiredAt > currentTimestamp
                  ? Math.ceil((license.expiredAt - currentTimestamp) / 86400)
                  : 0,
                title = license.isExpired
                  ? xcart.t('Your X-Cart trial has expired!')
                  : xcart.t('Your X-Cart trial expires in X day(s)', {count: remainingTrialDays});

          $(this).removeClass('hide');
          $(this).find('h2.title').html(title);

          if (license.isExpired) {
            $(this).find('.is-expired').removeClass('hide');

          } else {
            $(this).find('.is-not-expired').removeClass('hide');
          }

          $(this).find('.open-license-key-form').on('click', () => {
            $(this).parent().find('.activate-key-form').toggle();
            return false;
          });
        }
      });
    });
});
