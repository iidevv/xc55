/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

(function () {

  Vue.directive('xlitePattern', {
    inserted: function (el) {
      var inputmaskPattern = el.getAttribute('inputmask-pattern');
      Inputmask(JSON.parse(inputmaskPattern)).mask(el);
      setTimeout(() => Inputmask(JSON.parse(inputmaskPattern)).mask(el), 5000);
    }
  });

})();
