/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Auto display controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/autoDisplay', ['common/coreLicense', 'common/disallowedEnabledModules'], (coreLicense, disallowedEnabledModules) => {
  const cookieName = 'isTrialAutoDisplayNoticeShown';

  coreLicense.then(license => {
    if (license.isTrial && !jQuery.cookie(cookieName)) {
      popup.load(
        URLHandler.buildURL({
          target: 'trial_notice',
          widget: 'XLite\\View\\LicenseManager\\TrialNotice',
        }),
        () => {
          jQuery.cookie(cookieName, true, {
            path: '/'
          })
        }
      );

    }

    if (disallowedEnabledModules) {
      disallowedEnabledModules.then(modules => {
        if (modules.length > 0) {
          popup.load(
            URLHandler.buildURL({
              target: 'keys_notice',
              widget: 'XLite\\View\\LicenseManager\\KeysNotice',
            })
          );
        }
      });
    }
  });
});
