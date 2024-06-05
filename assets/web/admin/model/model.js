/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Model controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.bind('load', function (event) {
  var firstError = jQuery('.model-properties .table li.has-error').first();

  if (firstError.length > 0) {
    var timer = setInterval(function () {
      if (window.pageYOffset !== 0) {
        clearInterval(timer)
      } else {
        window.scrollTo(0, firstError.offset()['top']);
      }
    }, 1000);
  }
});
