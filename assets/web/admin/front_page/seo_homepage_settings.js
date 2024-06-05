/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * SEO Settings Homepage controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    jQuery('#meta-desc-type').change(
      function () {
        if (jQuery(this).val() == 'A') {
          jQuery('#meta-desc').prop('readonly', true)
        } else {
          jQuery('#meta-desc').prop('readonly', false)
        }

      }
    )

    jQuery('#meta-desc-type').change()


    var target_div = jQuery('form .table div.search-preview-value .input-internal-wrapper')
    jQuery('#name').bind(
      'input',
      function (event) {
        var result = true
        target_div.find('.company-name').get(0).innerHTML = this.value

        return result
      }
    )

    jQuery('#meta-desc').bind(
      'input',
      function (event) {
        var result = true
        target_div.find('.company-descr').get(0).innerHTML = this.value

        return result
      }
    )

    jQuery('#meta-tags').bind(
      'input',
      function (event) {
        var result = true
        target_div.find('.company-keywords').get(0).innerHTML = this.value

        return result
      }
    )
    jQuery('#posteddata-usecustomog').change(
      function () {
        if (jQuery(this).val() == '1') {
          jQuery('#posteddata-ogmeta').prop('readonly', false)
        } else {
          jQuery('#posteddata-ogmeta').prop('readonly', true)
        }

      }
    )

    jQuery('#posteddata-usecustomog').change()
  }
)
