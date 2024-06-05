/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Wave selector controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('waveSelector', ['common/coreLicense', 'js/vue/vue', 'js/jquery'], function (coreLicense, XLiteVue, $) {
  XLiteVue.component('wave-selector', {
    data: function () {
      return {
        waves: []
      };
    },

    methods: {
      setOptions: function () {
        const self = this;
        if (self.waves.length > 0) {
          self.waves.map(function (wave) {
            $('select#upgrade-wave').append('<option value="' + wave.id + '"' + (wave.isActive ? ' selected' : '') + '>' + wave.name + '</option>');
          });
        }
      }
    },

    created: function () {
      xcart.get(
        xliteConfig.base_url + 'service.php/api/waves',
        null,
        null,
        {
          dataType: 'json',
          success: data => {
            this.waves = data;
          }
        }
      );
    },

    watch: {
      waves: function () {
        this.setOptions()
      }
    },
  });

  coreLicense.then(license => {
    $('select#upgrade-wave').on('change', function () {
      const $this = $(this);
      const controller = $this.closest('form').get(0).commonController;

      controller.isChanged = () => false;

      $this.prop('disabled', true);

      xcart.post(
        '/service.php/api/licenses',
        null,
        JSON.stringify({
          licenseKey: license.keyValue,
          wave: $(this).val()
        }),
        {
          contentType: 'application/json',
          dataType: 'json',
          success: license => {
            self.license = license;

            xcart.trigger('message', {
              type: 'info',
              message: xcart.t('Upgrade access level is changed')
            });
          },
          error: xhr => {
            xcart.trigger('message', {
              type: 'error',
              message: xhr.responseJSON.detail
            });
          },
          complete: () => {
            $(this).prop('disabled', false);

            controller.isChanged = () => true;
          }
        }
      );
    });
  });
});
