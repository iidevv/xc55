/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

const sendGmvDataToMarketplaceRequestUrl = `${xliteConfig.base_url}service.php/save-gmv-data/`;

jQuery.ajax({
  url: sendGmvDataToMarketplaceRequestUrl,
  method: 'POST',
  async: true,
  dataType: 'json',
  cache: false,
  global: false,
}).done(function (data) {
  if (data.error) {
    console.error({
      'requestUrl': sendGmvDataToMarketplaceRequestUrl,
      'error': data.error,
      'message': data.message || '',
    });
  }
}).fail(function (xhr, textStatus, error) {
  console.error({
    'requestUrl': sendGmvDataToMarketplaceRequestUrl,
    error,
    textStatus
  })
})
