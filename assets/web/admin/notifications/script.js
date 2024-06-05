/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery.fn.extend({
  startTooltip: function() {
    $this = this;

    if (
      $this.closest('#form_customer').length
      || $this.closest('#form_admin').length
    ) {
      $this.on('show.bs.popover', function () {
        jQuery(this).data('bs.popover').options.placement =
          (jQuery(this).get(0).getBoundingClientRect().y < 400) ? 'bottom': 'top';
      });
    }
  },
});
