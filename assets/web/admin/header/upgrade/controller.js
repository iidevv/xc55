/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('js/xcart', [], () => {

  if (!xliteConfig.display_upgrade_notifications) {
    return null;
  }

  const url = xliteConfig.base_url;
  const upgradePage = window.location.hash.substring(2).split('/')[0] === 'upgrade';

  return xcart.get(
    url + 'service.php/api/module_upgrades',
    null,
    null,
    {
      dataType: 'json',
      success: result => {
        if (result.length) {
          const upgradeBox = jQuery('.upgrade-box');
          let upgrade = { minor: [], major: [] };

          _.filter(result, (item) => {
            if (Object.keys(item.buildUpgrade).length > 0) {
              upgrade.minor.push({ ...item.buildUpgrade, ...{ id: item.moduleId, type: 'minor' } });
            }

            if (Object.keys(item.minorUpgrade).length > 0) {
              upgrade.major.push({ ...item.minorUpgrade, ...{ id: item.moduleId, type: 'major' } });
            }

            if (Object.keys(item.majorUpgrade).length > 0) {
              upgrade.major.push({ ...item.majorUpgrade, ...{ id: item.moduleId, type: 'major' } });
            }
          });

          let upgradeData = upgrade.minor;
          let upgradeType = 'minor';
          const upgradeUrl = `${url}${xliteConfig.zone}/?target=apps#/upgrade`;

          if (upgrade.minor.length) {
            upgradeBox.addClass('minor').data('upgrade','minor');
          } else {
            upgradeBox.addClass('major').data('upgrade','major');

            upgradeData = upgrade.major;

            upgradeType = 'major';
          }

          if (!upgradePage) {
            upgradeBox.show();
          }

          const hasCoreUpgrade = _.where(upgradeData, {id: 'CDev-Core'}).length;
          let upgradeNote = xcart.t('Updates are available (new core)');
          const modulesCount = hasCoreUpgrade ? upgradeData.length - 1 : upgradeData.length;

          if (
            hasCoreUpgrade
            && upgradeData.length > 1
          ) {
            upgradeNote = modulesCount === 1
              ? xcart.t('Updates are available (new core and one addon)')
              : xcart.t('Updates are available (new core and N addons)', { modulesCount });
          } else if (!hasCoreUpgrade) {
            upgradeNote = modulesCount === 1
              ? xcart.t('Updates are available (one addon)')
              : xcart.t('Updates are available (N addons)', { modulesCount });
          }

          const button = upgradeBox.find('.upgrade-box__button');
          const upgradeMessageType = `upgrade-${upgradeType}`;

          button.on('click', function() {
            const topMessagesBox = jQuery('#status-messages');
            const alertBar = topMessagesBox.find('.upgrade-minor, .upgrade-major');

            if (!alertBar.length) {
              xcart.trigger(
                'message',
                {
                  'type': upgradeMessageType,
                  'message': `<a href="${upgradeUrl}/${upgradeType}">${upgradeNote}</a>`
                }
              );
            } else {
              alertBar.find('.close-message').trigger('click');
            }
          });

          xcart.microhandlers.add(
            'assignUpgradeNoteOnNotifications',
            '.infoblock-notifications .notifications',
            function() {
              const notificationCookieName = 'upgradeInfoNotification';

              if (jQuery.cookie(notificationCookieName) === 'closed') {
                return;
              }

              const upgradeNoteDashboard = xcart.t('Updates are available (N)', { modulesCount: upgradeData.length });

              const $notificationCloseBlock = jQuery('<div class="notification-close"><i class="fa-times"></i></div>');

              const upgradeDashboardAlert = jQuery(`
                <div class="infoblock-notification upgrade-info" data-notification-type="upgradeInfo"> 
                    <span class="notification-message"><a href="${upgradeUrl}/${upgradeType}">${upgradeNoteDashboard}</a></span>
                </div>
              `);

              upgradeDashboardAlert.append($notificationCloseBlock);

              jQuery(this).append(upgradeDashboardAlert);

              $notificationCloseBlock.on('click', function () {
                upgradeDashboardAlert.hide();
                jQuery.cookie(notificationCookieName, 'closed');
              });
            }
          );
        }
      }
    }
  );
});
