/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('common/marketplaceNotifications', ['common/coreLicense'], (coreLicense) => {
  const shopIdentifier = coreLicense.shopIdentifier;

  if (shopIdentifier) {
    loadMarketingInfo(shopIdentifier)
  }
});

const loadMarketingInfo = (shopIdentifier) => {
  const $marketingInfoBlocks = jQuery('[data-marketing-info-type]');

  if ($marketingInfoBlocks.length) {
    let pageQuery = window.location.search || '?target=main';
    pageQuery = encodeURIComponent(pageQuery.substring(1));

    const marketingInfoRequestUrl = `${xliteConfig.marketplace_api_url}get_commercials?`
      + `xc5_shop_identifier=${shopIdentifier}`
      + `&page_query=${pageQuery}`;

    jQuery.ajax({
      url: marketingInfoRequestUrl,
      async: true,
      dataType: 'json',
      cache: false,
      global: false,
    }).done(function (data) {
      if (data.error) {
        console.error({
          'requestUrl': marketingInfoRequestUrl,
          'error': data.error,
          'message': data.message || '',
        });
      } else {
        createMarketingInfoBlocks(data);
      }
    }).fail(function (xhr, textStatus, error) {
      console.error({
        'requestUrl': marketingInfoRequestUrl,
        error,
        textStatus,
      });
    });
  }
};

const createMarketingInfoBlocks = (marketingInfoItems) => {
  marketingInfoItems.forEach((marketingInfoItem) => {
    const $marketingInfoBlock = jQuery(`[data-marketing-info-type="${marketingInfoItem.type}"]`);

    if ($marketingInfoBlock.length) {
      switch (marketingInfoItem.type) {
        case 'banner':
          createBannerBlock(marketingInfoItem, $marketingInfoBlock);
          break;
        case 'news':
          createNotificationBlock(marketingInfoItem, $marketingInfoBlock);
          break;
        case 'warning':
          createNotificationBlock(marketingInfoItem, $marketingInfoBlock);
          break;
        case 'payment_banner':
          createPaymentBannerBlock(marketingInfoItem, $marketingInfoBlock);
          break;
        case 'landing_banner': // is not used now
          break;
        case 'module': // is not used now
          break;
      }
    }
  });
};

const createBannerBlock = (marketingInfoItem, $marketingInfoBlock) => {
  const marketingInfoItemId = getMarketingInfoId(marketingInfoItem);

  if (jQuery.cookie(marketingInfoItemId) === 'closed') {
    return;
  }

  const $bannerLogoBlock = jQuery(`<div class="promo-banner-logo"></div>`);
  $bannerLogoBlock.append(
    getBannerImageBlock(marketingInfoItem),
  );

  const $bannerContentBlock = jQuery(`<div class="promo-banner-content"></div>`);
  $bannerContentBlock.append(
    getBannerDescriptionBlock(marketingInfoItem),
  );

  const $bannerCloseBlock = jQuery('<div class="promo-banner-close"><i class="fa-times"></i></div>');

  const $banner = jQuery('<div class="promo-banner"></div>');
  $banner.append($bannerLogoBlock, $bannerContentBlock, $bannerCloseBlock);

  $marketingInfoBlock.append($banner);

  $bannerCloseBlock.on('click', function () {
    $banner.hide();
    jQuery.cookie(marketingInfoItemId, 'closed');
  });
};

const createNotificationBlock = (marketingInfoItem, $marketingInfoBlock) => {
  const marketingInfoItemId = getMarketingInfoId(marketingInfoItem);

  if (jQuery.cookie(marketingInfoItemId) === 'closed') {
    return;
  }

  const $notificationTitleBlock = jQuery('<span class="notification-message"></span>');
  $notificationTitleBlock.append(
    getNotificationTitleBlock(marketingInfoItem),
  );

  const $notificationCloseBlock = jQuery('<div class="notification-close"><i class="fa-times"></i></div>');

  const $notificationBlock = jQuery(`<div class="js-infoblock-notification"></div>`);
  $notificationBlock.append($notificationTitleBlock, $notificationCloseBlock);

  $marketingInfoBlock.append($notificationBlock);

  $notificationCloseBlock.on('click', function () {
    $notificationBlock.hide();
    jQuery.cookie(marketingInfoItemId, 'closed');
  });
};

const createPaymentBannerBlock = (marketingInfoItem, $marketingInfoBlock) => {
  $marketingInfoBlock.append(
    getBannerImageBlock(marketingInfoItem),
  );
};

const getBannerImageBlock = (marketingInfoItem) => {
  const $bannerImage = jQuery(`<img src="${marketingInfoItem.image}"/>`);
  const url = getBannerUrl(marketingInfoItem);

  return url
    ? getLinkBlock(url, $bannerImage)
    : $bannerImage;
};

const getMarketingInfoId = (marketingInfoItem) => {
  return `${marketingInfoItem.type}_${marketingInfoItem.id}`;
}

const getBannerDescriptionBlock = (marketingInfoItem) => {
  const bannerDescription = marketingInfoItem.description;
  const url = getBannerUrl(marketingInfoItem);

  return url
    ? getLinkBlock(url, bannerDescription)
    : bannerDescription;
};

const getNotificationTitleBlock = (marketingInfoItem) => {
  const notificationTitle = marketingInfoItem.title;
  const url = marketingInfoItem.link;

  return url
    ? getLinkBlock(url, notificationTitle, true)
    : notificationTitle;
};

const getLinkBlock = (url, linkContent, hasUtm = false) => {
  if (hasUtm) {
    url = getUrlWithUtm(url);
  }

  const $linkBlock = jQuery(`<a href="${url}" target="_blank"></a>`);
  $linkBlock.append(linkContent);

  return $linkBlock;
};

const getUrlWithUtm = (url) => {
  const urlObject = new URL(url);

  urlObject.searchParams.set('utm_source', 'xc5admin');
  urlObject.searchParams.set('utm_medium', 'link2blog');
  urlObject.searchParams.set('utm_campaign', 'xc5adminlink2blog');

  return urlObject.toString();
}

const getBannerUrl = (marketingInfoItem) => {
  let result = null;

  if (marketingInfoItem.link) {
    result = marketingInfoItem.link;
  } else if (marketingInfoItem.module) {
    result = `?target=apps#/installed-addons?moduleId=${marketingInfoItem.module}`;
  }

  return result;
};
