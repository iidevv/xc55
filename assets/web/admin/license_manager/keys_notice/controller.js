/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * License warning widget controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('showTrialLicenseNotice', ['common/coreLicense'], coreLicense => {
  let disallowedModulesData = {};
  let xbids = [];

  xcart.microhandlers.add(
    'keysNoticeForm',
    '.keys-notice-form',
    function () {
      const form           = jQuery(this),
            moduleList     = form.find('.modules-list'),
            moduleTemplate = moduleList.find('.module-info.template'),
            reCheckButton  = form.find('.btn.recheck-license'),
            handler        = () => {
              moduleList.find('.module-info:not(.template)').remove();
              setTimeout(() => assignWaitOverlay(form), 100);
              const url = xliteConfig.base_url;

              xcart.get(
                url + 'service.php/api/modules/disallowed',
                null,
                null,
                {
                  dataType: 'json',
                  success: data => {
                    if (data.length > 0) {
                      unassignWaitOverlay(form);
                      data.forEach(function (module) {
                        disallowedModulesData = {
                          ...disallowedModulesData,
                          [`${module.author}-${module.name}`]: {
                            type: 'remove',
                            moduleName: module.readableName,
                          },
                        }

                        xbids.push(module.xbProductId);

                        let item = moduleTemplate.clone();
                        item.removeClass('template hide');
                        item.find('.module-name').html(module.readableName);
                        item.find('.module-reason').html(module.reason);
                        if (module.purchaseUrl) {
                          item.find('.module-action a').attr('href', module.purchaseUrl);
                        }
                        item.appendTo(moduleList);
                      });
                    }
                  }
                });
            };

      form.find(coreLicense.isTrial ? '.is-trial' : '.is-edition').removeClass('hide');

      reCheckButton.on('click', () => {
        handler();
      });

      handler();
    }
  );

  xcart.microhandlers.add(
    'handleRemoveUnallowedModules',
    '#remove-unallowed-modules',
    function () {
      const self = this;
      const url = `${xliteConfig.base_url}service.php/api/scenarios`;

      self.addEventListener('click', function () {
        if (!disallowedModulesData) {
          return;
        }

        const transitions = () => {
          const result = {
            modulesToRemove: [],
          };

          Object.keys(disallowedModulesData).forEach((moduleId) => {
            result.modulesToRemove.push(moduleId);
          });

          return result;
        };

        const options = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(transitions()),
        };

        fetch(url, options).then(async (data) => {
          const response = await data.json().then(jsonData => jsonData);

          if (!response || response.errorType === 'alreadyStarted') {
            return Promise.reject(response.errorType);
          }

          if (response.id) {
            const encodedCurrentUrl = encodeURIComponent(window.location.href);

            window.location.href =
              `${xliteConfig.base_url}rebuild.html?scenarioId=${response.id}&returnURL=${encodedCurrentUrl}`;
          }

          return true;
        }).catch((error) => {
          // eslint-disable-next-line no-console
          console.error('There was an error:', error);
        });
      });
    }
  );

  xcart.microhandlers.add(
    'handlePurchaseLicense',
    '#purchase-license',
    function () {
      const self = this;
      const marketUrl = new URL(self.href);

      let xbid_params = {};

      self.addEventListener('click', (e) => {
        e.preventDefault();

        if (xbids.length) {
          xbids.map((id, index) => {
            xbid_params = { ...xbid_params, ...{ [`xbid_${index + 1}`]: id } };
          });

          const newMarketUrlParams = new URLSearchParams([
            ...Array.from(marketUrl.searchParams.entries()),
            ...Object.entries(xbid_params)
          ]);

          const newMarketUrl = new URL(`${marketUrl}${newMarketUrlParams.toString()}`);

          window.open(newMarketUrl.toString(), '_blank');
        }
      });
    }
  );
});
