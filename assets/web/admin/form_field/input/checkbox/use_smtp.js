/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Use smtp script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function () {
  xcart.microhandlers.add(
      'auth_email_from',
      'select[name="smtp_auth_mode"]',
      function() {
        $(this).change(function () {
          if ($(this).val() === 'custom') {
            $('[name="smtp_username"]').closest('li.input').show();
            $('[name="smtp_password"]').closest('li.input').show();
            $('[name="smtp_security"]').closest('li.input').show();
            $('[name="smtp_server_url"]').closest('li.input').show();
            $('[name="smtp_server_port"]').closest('li.input').show();
            $('[name="smtp_client_id"]').closest('li.input').hide();
            $('[name="smtp_secret_key"]').closest('li.input').hide();
            $('[name="smtp_token_link"]').closest('li.input').hide();
            $('[name="smtp_redirect_url"]').closest('li.input').hide();
          } else if (
              $(this).val() === 'google'
              || $(this).val() === 'yahoo'
              || $(this).val() === 'microsoft'
          ) {
            $('[name="smtp_username"]').closest('li.input').hide();
            $('[name="smtp_password"]').closest('li.input').hide();
            $('[name="smtp_security"]').closest('li.input').hide();
            $('[name="smtp_server_url"]').closest('li.input').hide();
            $('[name="smtp_server_port"]').closest('li.input').hide();
            $('[name="smtp_redirect_url"]').closest('li.input').show();
            $('[name="smtp_client_id"]').closest('li.input').show();
            $('[name="smtp_secret_key"]').closest('li.input').show();
            $('[name="smtp_token_link"]').closest('li.input').show();
          }
        });
      }
  );

  xcart.microhandlers.add(
    'use_smtp',
    'input[name="use_smtp"]',
    function() {
      $(this).change(function () {
        if ($(this).prop('checked')) {
          $('[name="smtp_server_url"]').closest('li.input').show();
          $('[name="smtp_server_port"]').closest('li.input').show();
          $('[name="smtp_auth_mode"]').closest('li.input').show();
          $('[name="smtp_username"]').closest('li.input').show();
          $('[name="smtp_password"]').closest('li.input').show();
          $('[name="smtp_security"]').closest('li.input').show();
          $('[name="smtp_client_id"]').closest('li.input').show();
          $('[name="smtp_secret_key"]').closest('li.input').show();
          $('[name="smtp_redirect_url"]').closest('li.input').show();
          $('.input-checkbox-usesmtp .help-block').show();
          $('select[name="smtp_auth_mode"]').change();
        } else {
          $('[name="smtp_server_url"]').closest('li.input').hide();
          $('[name="smtp_server_port"]').closest('li.input').hide();
          $('[name="smtp_auth_mode"]').closest('li.input').hide();
          $('[name="smtp_username"]').closest('li.input').hide();
          $('[name="smtp_password"]').closest('li.input').hide();
          $('[name="smtp_security"]').closest('li.input').hide();
          $('[name="smtp_client_id"]').closest('li.input').hide();
          $('[name="smtp_secret_key"]').closest('li.input').hide();
          $('[name="smtp_token_link"]').closest('li.input').hide();
          $('[name="smtp_redirect_url"]').closest('li.input').hide();
          $('.input-checkbox-usesmtp .help-block').hide();
        }
      }).change();
    }
  );
});
