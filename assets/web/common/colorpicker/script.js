/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * ColorPicker input field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('field/colorpicker/pickr',
  ['colorpicker/pickr'],
  function (ColorPicker) {
    CommonElement.prototype.handlers.push({
      canApply: function () {
        return this.$element.is('input.color-picker-field');
      },
      handler: function () {
        let self = this;
        self.colorpicker = new ColorPicker(this.$element);
      }
    });
  }
);