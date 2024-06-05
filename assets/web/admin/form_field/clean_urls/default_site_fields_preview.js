/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * CleanUrls\DefaultSiteFieldsPreview field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '#default-site-title',
    handler: function () {

      var target_div = jQuery('form .table div.default-site-meta-fields-preview')
      jQuery('#default-site-title').bind(
        'input',
        function (event) {
          var result = true
          target_div.find('.company-name').get(0).innerHTML = this.value

          return result
        }
      )

      jQuery('#default-site-meta-description').bind(
        'input',
        function (event) {
          var result = true
          target_div.find('.company-descr').get(0).innerHTML = this.value

          return result
        }
      )

      jQuery('#default-site-meta-keywords').bind(
        'input',
        function (event) {
          var result = true
          target_div.find('.company-keywords').get(0).innerHTML = this.value

          return result
        }
      )

    }
  }
)
