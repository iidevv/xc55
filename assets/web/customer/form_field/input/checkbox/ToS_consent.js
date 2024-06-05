/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * ToS consent controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.bind([
  'checkout.common.ready',
  'checkout.common.nonready'
], function () {
  let $element  = $('input#tosconsent');
  if (!$element.prop('checked')) {
    xcart.trigger(
      'message',
      {'type': 'warning', 'message': xcart.t('You have to accept terms & conditions')}
    );
    $element.get(0).commonController.markAsInvalid();
  }
});
