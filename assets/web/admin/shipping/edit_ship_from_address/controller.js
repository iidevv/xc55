/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function UseCompanyAddressSwitcher () {
  function checkFormFieldsAvailability() {
    const $useCompanyAddress = $('#origin-use-company');
    const $table = $useCompanyAddress.closest('.table');
    const isUsedCompanyAddress = $useCompanyAddress.is(':checked');

    $('input:not([name="origin_use_company"]), select', $table).each(function () {
      $(this).prop('disabled', isUsedCompanyAddress);
    })
  };

  checkFormFieldsAvailability();
  $('#origin-use-company').change(checkFormFieldsAvailability);
}

xcart.autoload(UseCompanyAddressSwitcher);
