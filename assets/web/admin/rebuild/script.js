/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function Rebuild(scenarioData) {
  this.scenarioData = scenarioData;
}

Rebuild.prototype.run = function () {
  const self = this;
  const url = xliteConfig.base_url;

  $.ajax({
    type: 'POST',
    contentType: 'application/json',
    dataType: 'json',
    url: url + 'service.php/api/scenarios',
    data: JSON.stringify(this.scenarioData),
    success: function (data, textStatus, xhr) {
      if (data.error) {
        let errorMessage;

        if (data.errorType === 'alreadyStarted') {
          errorMessage = xcart
            .t('Hold on a moment, please. Redeploy is in progress')
            .replace(
              '$rebuildLink',
              self.getRebuildPageLink(data.scenarioId),
            );
        } else {
          errorMessage = xcart.t('Could not create a rebuild scenario');

          console.error(data.error);
        }

        xcart.trigger(
          'message',
          {
            type: 'error',
            message: errorMessage,
          },
        );

        return;
      }

      if (data.id) {
        window.location.href = self.getRebuildPageLink(data.id);
      } else {
        console.error('Empty scenario id');
        xcart.trigger(
          'message',
          {
            type: 'error',
            message: xcart.t('Could not create a rebuild scenario'),
          },
        );
      }
    },
    error: function (xhr, textStatus, errorThrown) {
      console.error(errorThrown);

      xcart.trigger(
        'message',
        {
          type: 'error',
          message: xcart.t('Could not create a rebuild scenario'),
        },
      );
    },
  });
};

Rebuild.prototype.getRebuildPageLink = function (scenarioId) {
  const encodedCurrentUrl = encodeURIComponent(window.location.href);
  const url = xliteConfig.base_url.slice(0, -1);
  const publicDir = xliteConfig.public_dir;
  sessionStorage.setItem('xcUrl', url);

  return `${url}${publicDir}/rebuild.html?scenarioId=${scenarioId}&returnURL=${encodedCurrentUrl}`;
};
