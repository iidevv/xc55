/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product attributes functions
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Get product attribute element by name
function product_attribute(name_of_attribute)
{
  var e = jQuery('form[name="add_to_cart"] :input').filter(
    function() {
      return this.name && this.name.search(name_of_attribute) !== -1;
    }
  );

  return e.get(0);
}

let textAttrCache = [];

function getAttributeValuesParams(product)
{
  let activeAttributeValues = '';
  let base = '.product-info-' + product.product_id;

  jQuery("ul.attribute-values input[type=checkbox]", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_';
    activeAttributeValues += jQuery(elem).is(":checked") ?  jQuery(elem).val() : jQuery(elem).data('unchecked');
    activeAttributeValues += ',';
  });

  jQuery("ul.attribute-values select", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_' + jQuery(elem).val() + ',';
  });

  jQuery("ul.attribute-values textarea", jQuery(base).last()).each(function(index, elem) {
    textAttrCache[jQuery(elem).data('attribute-id')] = jQuery(elem).val();
  });

  jQuery("ul.attribute-values input.blocks-input", jQuery(base).last()).each(function(index, elem) {
    activeAttributeValues += jQuery(elem).data('attribute-id') + '_' + jQuery(elem).val() + ',';
  });

  return {
    attribute_values: activeAttributeValues
  };
}

/**
 * Product attributes triggers are inputs and selectors
 * of the attribute-values block
 *
 * @returns {String}
 */
function getAttributeValuesTriggers()
{
  return 'ul.attribute-values input, ul.attribute-values select';
}

function getAttributeValuesShadowWidgets()
{
  return '.widget-fingerprint-product-price';
}

function bindAttributeValuesTriggers()
{
  const handler = function (productId) {
    xcart.trigger('update-product-page', productId);
  };

  const obj = jQuery("ul.attribute-values").closest('.product-details-info').find('form.product-details');
  if (obj.length > 0) {
    const productId = jQuery('input[name="product_id"]', obj).val();

    jQuery("ul.attribute-values input[type='checkbox']").unbind('change').bind('change', function () { handler(productId); });
    jQuery("ul.attribute-values input.blocks-input").unbind('change').bind('change', function () { handler(productId); });
    jQuery("ul.attribute-values select").unbind('change').bind('change', function () { handler(productId); });

    jQuery("ul.attribute-values textarea").each(function(index, elem) {
      if (textAttrCache[jQuery(elem).data('attribute-id')]) {
        jQuery(elem).val(textAttrCache[jQuery(elem).data('attribute-id')]);
      }
    });
  }
}

function BlocksSelector()
{
  jQuery('.product-details-info').on(
    'click',
    '.block-value:not(.selected):not(.unavailable)',
    function () {
      const $blockValue = jQuery(this);
      const blockValueId = $blockValue.data('value');
      const blockValueName = $blockValue.find('.block-value-name').html();
      const blockValueModifiers = $blockValue.data('modifiers');
      const blocksName = $blockValue.data('name');

      const $blocksSelector = $blockValue.closest('.blocks-selector');
      const $blocksTitle = $blocksSelector.find('.blocks-title');
      const $blocksInput = $blocksSelector.find('.blocks-input');

      $blocksTitle.removeClass('not-selected');
      $blocksInput.val(blockValueId).change();
      $blocksTitle.find('.attr-value-name').html(blockValueName);
      $blocksTitle.find('.attr-value-modifiers').html(blockValueModifiers);

      unselectAllBlocks(blocksName);
      $blockValue.addClass('selected');
    }
  );

  let timeout;
  let $tooltip;

  setTimeout(() => {
    jQuery('.product-details-info, .change-attribute-values').on({
      mouseenter: function () {
        $tooltip = jQuery(this).find('.unavailable-tooltip');
        timeout = setTimeout(function () {
          $tooltip.show(100);
        }, 250);
      },
      mouseleave: function () {
        clearTimeout(timeout);
        if ($tooltip !== undefined) {
          $tooltip.hide(100);
        }
      }
    }, '.block-value');
  }, 1);
}

function unselectAllBlocks(blocksName) {
  jQuery(`.block-value[data-name="${blocksName}"]`).removeClass('selected');
}

xcart.autoload(BlocksSelector);
xcart.bind('block.product.details.postprocess', BlocksSelector);
xcart.registerWidgetsParamsGetter('update-product-page', getAttributeValuesParams);
xcart.registerWidgetsTriggers('update-product-page', getAttributeValuesTriggers);
xcart.registerTriggersBind('update-product-page', bindAttributeValuesTriggers);
xcart.registerShadowWidgets('update-product-page', getAttributeValuesShadowWidgets);
xcart.registerShadowWidgets('update-product-page', function() {
  return '.widget-fingerprint-common-attributes';
});
