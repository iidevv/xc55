/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Decorator for bootstrap popover plugin
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

if ($.fn.popover) {
  let myDefaultWhiteList = $.fn.popover.Constructor.DEFAULTS.whiteList;

  myDefaultWhiteList.input = [];
  myDefaultWhiteList.textarea = [];
  myDefaultWhiteList.select = [];
  myDefaultWhiteList.button = [];
  myDefaultWhiteList.table = [];
  myDefaultWhiteList.th = [];
  myDefaultWhiteList.tr = [];
  myDefaultWhiteList.td = [];

  myDefaultWhiteList['*'].push('type', 'width', 'height');
}
