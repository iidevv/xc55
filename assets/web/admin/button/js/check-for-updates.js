/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Check for update button controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


function CheckForUpdatesButton()
{
  CheckForUpdatesButton.superclass.constructor.apply(this, arguments);
}

// New POPUP button widget extends POPUP button class
extend(CheckForUpdatesButton, ProgressStateButton);

CheckForUpdatesButton.autoload = function () {
  jQuery('.btn.check-for-updates').each(
    function () {
      if (!this.checkForUpdates) {
        var checkForUpdates = new CheckForUpdatesButton(this);

        this.checkForUpdates = checkForUpdates;

        jQuery(this).click(function () {
          checkForUpdates.clearCache();
        });
      };
    }
  );
};

CheckForUpdatesButton.prototype.clearCache = function() {
  var obj = this;
  xcart.get(
    URLHandler.buildURL({
      'base': 'service.php?/clear-cache',
    }),
    function(xhr, status, data) {
      obj.setStateSuccess();
      self.location.reload(true);
    }
  );
};

xcart.autoload(CheckForUpdatesButton);

xcart.microhandlers.add(
  'CheckForUpdatesButton',
  '.btn.check-for-updates',
  function (event) {
    xcart.autoload(CheckForUpdatesButton);
  }
);
