/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * page title format preview
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery().ready(
  function () {
    var draw_titles = function () {
      var _reverse = function (arr) {
        return jQuery('#object-name-in-page-title-order').prop('checked') ? arr.reverse() : arr
      }

      var target_div = jQuery('#page-title-preview')
      var position_logic_all = new Map([
        ['category', new Map([
          ['11', [target_div.data('companyName'), target_div.data('parentCategoryName'), target_div.data('categoryName')]],
          ['01', [target_div.data('parentCategoryName'), target_div.data('categoryName')]],
          ['10', [target_div.data('companyName'), target_div.data('categoryName')]],
          ['00', [target_div.data('categoryName')]]
        ])],
        ['product', new Map([
          ['11', [target_div.data('companyName'), target_div.data('parentCategoryName'), target_div.data('productName')]],
          ['01', [target_div.data('parentCategoryName'), target_div.data('productName')]],
          ['10', [target_div.data('companyName'), target_div.data('productName')]],
          ['00', [target_div.data('productName')]]
        ])],
        ['page', new Map([
          ['11', [target_div.data('companyName'), target_div.data('pageName')]],
          ['01', [target_div.data('pageName')]],
          ['10', [target_div.data('companyName'), target_div.data('pageName')]],
          ['00', [target_div.data('pageName')]]
        ])]
      ])

      var company_name_checkbox = jQuery('#company-name').prop('checked') ? '1' : '0'
      var parent_category_checkbox = jQuery('#parent-category-path').prop('checked') ? '1' : '0'
      const cat_prod = ['category', 'product', 'page']
      cat_prod.forEach(function (element) {
        var position_logic = position_logic_all.get(element)
        target_div.find('.' + element + ' .content').get(0).innerHTML = _reverse(position_logic.get(company_name_checkbox + parent_category_checkbox)).join(target_div.data('delimiter'))
      })
    }
    draw_titles()

    jQuery('#company-name').on('click', draw_titles)
    jQuery('#parent-category-path').on('click', draw_titles)
    jQuery('#object-name-in-page-title-order').on('click', draw_titles)
  }
)

