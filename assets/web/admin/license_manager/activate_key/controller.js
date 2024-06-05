/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Activate license key
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

xcart.microhandlers.add(
  'ActivateKeyActivateKeyFormPopup',
  '.activate-key-block .open-license-key-form',
  function () {
    jQuery(this).click(function () {
      jQuery(this).parents('.activate-key-block').find('.activate-key-form').toggle();
      return false;
    })
  }
);

define('activateKeyHandler', ['common/activateKeyHandler'], activateKeyHandler => {
  xcart.microhandlers.add(
    'ActivateKeyActivateKeyForm',
    '.activate-key-form',
    function () {
      jQuery(this).find('button').on('click', function () {
        const form = $(this).closest('form').get(0);
        const formController = form.commonController;
        formController.preprocessBackgroundSubmit();

        activateKeyHandler(
          () => {
            formController.postprocessBackgroundSubmit();
            $(form).find('input').val('');
            _.delay(() => popup.close(), 300);
          },
          () => formController.postprocessBackgroundSubmit(),
        ).handle(Object.fromEntries(new FormData(form)));
      });
    });
});
