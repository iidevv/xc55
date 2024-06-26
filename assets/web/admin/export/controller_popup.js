/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Import / export controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupExportController() {
  jQuery('.export-progress .bar')
    .bind(
      'error',
      function() {
        this.errorState = true;
        xcart.trigger('export.failed');
      }
    )
    .bind(
      'cancel',
      function() {
        xcart.trigger('export.canceled');
      }
    )
    .bind(
      'complete',
      function() {
        if (!this.errorState) {
          xcart.trigger('export.completed');
        }
      }
    );
}
