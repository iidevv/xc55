/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-states-select2 select',
    handler: function () {
      // Clean up after previous render
      $(this).next('.select2').remove();

      $(this).select2({
        language: {
          noResults: function(){
            return xcart.t('No results found.');
          }
        },
        escapeMarkup: function (markup) {
          return markup;
        },
        templateSelection: function (data) {
          return data.text;
        }
      });
    }
  }
);
