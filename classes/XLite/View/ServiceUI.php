<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * UI instances
 *
 * @ListChild (list="admin.center", zone="admin", weight="99999999")
 */
class ServiceUI extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     */
    public static function getAllowedTargets(): array
    {
        return array_merge(parent::getAllowedTargets(), ['apps']);
    }

    /**
     * Return widget default template
     */
    protected function getDefaultTemplate(): string
    {
        return 'service/body.twig';
    }

    /**
     * Get compiled app js file
     */
    protected function getAppJs(): array
    {
        return [
            'service/dist/js/manifest.min.js',
            'service/dist/js/vendor.min.js',
            'service/dist/js/app.min.js',
        ];
    }

    /**
     * Get compiled app css file
     */
    public function getCSSFiles(): array
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'service/dist/css/app.min.css',
            ]
        );
    }

    /**
     * Get X-Cart config details
     */
    protected function getHostDetails(): array
    {
        return \Includes\Utils\ConfigParser::getOptions(['host_details']);
    }

    /**
     * Get web dir
     */
    protected function getWebDir(): string
    {
        $hostDetails = $this->getHostDetails();

        return !empty($hostDetails['web_dir'])
            ? ltrim($hostDetails['web_dir'], '/')
            : '';
    }

    /**
     * Get admin self script
     */
    protected function getAdminSelf(): string
    {
        $hostDetails = $this->getHostDetails();

        return !empty($hostDetails['admin_self'])
            ? trim($hostDetails['admin_self'], '/')
            : '';
    }

    /**
     * Get host
     */
    protected function getHost(): string
    {
        $hostDetails = $this->getHostDetails();

        return 'http' . (static::isHTTPS() ? 's' : '') . '://' . $hostDetails['http_host'];
    }

    /**
     * Get public dir
     */
    protected function getPublicDir(): string
    {
        $hostDetails = $this->getHostDetails();

        return $hostDetails['public_dir'] ? 'public' : '';
    }

    protected function getAppStoreUrl(): string
    {
        $marketplace = \Includes\Utils\ConfigParser::getOptions(['marketplace']) ?? [];

        return $marketplace['appstore_url'] ?? '';
    }

    protected function getAddonImagesUrl(): string
    {
        $marketplace = \Includes\Utils\ConfigParser::getOptions(['marketplace']) ?? [];

        return $marketplace['addon_images_url'] ?? '';
    }

    protected function getXbHost(): string
    {
        $marketplace = \Includes\Utils\ConfigParser::getOptions(['marketplace']) ?? [];

        return $marketplace['xb_host'] ?? '';
    }

    /**
     * Whether the current user is root admin.
     */
    protected function isRootAdmin(): bool
    {
        $profile = Auth::getInstance()->getProfile();

        return $profile && $profile->isAdmin() && $profile->isPermissionAllowed('root access');
    }

    /**
     * Prepare global config obj
     */
    protected function getCommentedData(): array
    {
        return ['appConfig' => [
            'url'            => !empty($this->getWebDir())
                ? $this->getHost() . '/' . $this->getWebDir()
                : $this->getHost(),
            'host'           => $this->getHost(),
            'adminScript'    => $this->getAdminSelf(),
            'publicDir'      => $this->getPublicDir(),
            'addonImagesUrl' => $this->getAddonImagesUrl(),
            'extAppStoreUrl' => $this->getAppStoreUrl(),
            'xbHost'         => $this->getXbHost(),
            'isRootAdmin'    => $this->isRootAdmin(),
            'languages'      => [\XLite\Core\Session::getInstance()->getLanguage()->getCode()],
            'session'        => md5(\XLite\Core\Session::getInstance()->getSessionId()),
            'isDemoMode'     => $this->isDemoMode(),
            'messages'       => [
                \XLite\Core\Session::getInstance()->getLanguage()->getCode() => [
                    'navigation.my_addons'                                                      => static::t('navigation.my_addons'),
                    'Enabled'                                                                   => static::t('Enabled'),
                    "Disabled"                                                                  => static::t('Disabled'),
                    'Author'                                                                    => static::t('Author'),
                    'Version'                                                                   => static::t('Version'),
                    'Description'                                                               => static::t('Description'),
                    'Items per page'                                                            => static::t('Items per page'),
                    'Settings'                                                                  => static::t('Settings'),
                    'Manage layout'                                                             => static::t('Manage layout'),
                    'modules-page.switch-to-grid-view'                                          => static::t('modules-page.switch-to-grid-view'),
                    'modules-page.switch-to-list-view'                                          => static::t('modules-page.switch-to-list-view'),
                    'modules-list-controls.installed.all'                                       => static::t('modules-list-controls.installed.all'),
                    'module.view_details.marketplace'                                           => static::t('module.view_details.marketplace'),
                    'module_action.remove'                                                      => static::t('Remove'),
                    'Only enabled'                                                              => static::t('Only enabled'),
                    'Only disabled'                                                             => static::t('Only disabled'),
                    'modules-list-controls.installed.recent'                                    => static::t('modules-list-controls.installed.recent'),
                    'scenario.transition.stateToSet.enabled'                                    => static::t('On'),
                    'scenario.transition.stateToSet.installed'                                  => static::t('Off'),
                    'scenario.transition.stateToSet.removed'                                    => static::t('Remove'),
                    'scenario.transition.module.successful'                                     => static::t('Add-on(s) are successfully installed and enabled.'),
                    'scenario.transition.module.err.BE.cant-disable-by-back-dependency'         => static::t('scenario.transition.module.err.BE.cant-disable-by-back-dependency'),
                    'scenario.transition.module.err.BE.cant-disable-by-module-property'         => static::t('scenario.transition.module.err.BE.cant-disable-by-module-property'),
                    'scenario.transition.module.err.BE.cant-enable-by-dependency'               => static::t('scenario.transition.module.err.BE.cant-enable-by-dependency'),
                    'scenario.transition.module.err.BE.cant-enable-by-incompatible'             => static::t('scenario.transition.module.err.BE.cant-enable-by-incompatible'),
                    'scenario.transition.module.err.BE.cant-enable-by-minorRequiredCoreVersion' => static::t('scenario.transition.module.err.BE.cant-enable-by-minorRequiredCoreVersion'),
                    'scenario.transition.module.err.BE.conflict-with-disable'                   => static::t('scenario.transition.module.err.BE.conflict-with-disable'),
                    'scenario.transition.module.err.BE.conflict-with-enable'                    => static::t('scenario.transition.module.err.BE.conflict-with-enable'),
                    'scenario.transition.module.err.BuildException'                             => static::t('The add-on cannot be installed.'),
                    'scenario.transition.module.err.CheckPermissionsException'                  => static::t('The add-on cannot be installed. No write permissions to the directory.'),
                    'scenario.transition.module.err.CircularDependencyException'                => static::t('The add-on cannot be installed. Cycle found in dependencies or incompatibles'),
                    'scenario.transition.module.err.DownloadModulesException'                   => static::t('The add-on cannot be downloaded.'),
                    'scenario.transition.module.err.DownloadPackException'                      => static::t('No valid X-Cart license can be found for the add-on.'),
                    'scenario.transition.module.err.ElementNotFoundException'                   => static::t('The add-on cannot be installed.'),
                    'module_alert.moduleName'                                                   => static::t('Name and author of the module'),
                    'cant-disable-by-back-dependency'                                           => static::t('Cannot be disabled. The module is required by: {0}'),
                    'cant-disable-by-module-property'                                           => static::t('The module may not be disabled due to the limitations of the module architecture.'),
                    'cant-enable-by-incompatible'                                               => static::t('To enable this addon, the following addon(s) must be disabled: {0}'),
                    'cant-enable-by-dependency'                                                 => static::t('To enable this addon, the following addon(s) must be enabled: {0}'),
                    'Hold on a moment, please. Redeploy is in progress'                         => static::t('Hold on a moment, please. Redeploy is in progress'),
                    'license.success-message.core'                                              => static::t('X-Cart license key has been successfully verified'),
                    'license.success-message.module'                                            => static::t('License key has been successfully verified and activated for "{name}" module by "{author}" author.'),
                    'Enter your license key'                                                    => static::t('Enter your license key'),
                    'Activate License Key'                                                      => static::t('Activate License Key'),
                    'Register'                                                                  => static::t('Register'),
                    'Selected modules'                                                          => static::t('Selected modules'),
                    'will be'                                                                   => static::t('will be'),
                    'No modules selected'                                                       => static::t('No modules selected'),
                    'Clear all'                                                                 => static::t('Clear all'),
                    'upgrade-page-heading'                                                      => static::t('upgrade-page-heading'),
                    'empty-upgrade.info'                                                        => static::t('empty-upgrade.info'),
                    'upgrade-preview.title.delayed'                                             => static::t('upgrade-preview.title.delayed'),
                    'upgrade-preview.title.minor'                                               => static::t('upgrade-preview.title.minor'),
                    'upgrade-preview.title.major'                                               => static::t('upgrade-preview.title.major'),
                    'upgrade-preview.title.with-core.minor'                                     => static::t('upgrade-preview.title.with-core.minor'),
                    'upgrade-preview.title.with-core.major'                                     => static::t('upgrade-preview.title.with-core.major'),
                    'upgrade-preview.changelog-message.delayed'                                 => static::t('upgrade-preview.changelog-message.delayed'),
                    'upgrade-preview.changelog-message.minor'                                   => static::t('upgrade-preview.changelog-message.minor'),
                    'upgrade-preview.changelog-message.major'                                   => static::t('upgrade-preview.changelog-message.major'),
                    'upgrade-preview.changelog-link'                                            => static::t('upgrade-preview.changelog-link'),
                    'upgrade-preview.button-title.delayed'                                      => static::t('upgrade-preview.button-title.delayed'),
                    'upgrade-preview.button-title.minor'                                        => static::t('upgrade-preview.button-title.minor'),
                    'upgrade-preview.button-title.major'                                        => static::t('upgrade-preview.button-title.major'),
                    'changelog-dialog.title.minor'                                              => static::t('changelog-dialog.title.minor'),
                    'changelog-dialog.title.major'                                              => static::t('changelog-dialog.title.major'),
                    'changelog-dialog.module.title'                                             => static::t('changelog-dialog.module.title'),
                    'upgrade-details-title.delayed'                                             => static::t('upgrade-details-title.delayed'),
                    'upgrade-details-title.minor'                                               => static::t('upgrade-details-title.minor'),
                    'upgrade-details-title.major'                                               => static::t('upgrade-details-title.major'),
                    'upgrade-details-page.advanced-mode.enabled'                                => static::t('upgrade-details-page.advanced-mode.enabled'),
                    'upgrade-details-page.advanced-mode.disabled'                               => static::t('upgrade-details-page.advanced-mode.disabled'),
                    'upgrade-details-page.need-help-backup'                                     => static::t('upgrade-details-page.need-help-backup'),
                    'upgrade-details-page.disabled-and-custom-modules-warning.title'            => static::t('upgrade-details-page.disabled-and-custom-modules-warning.title'),
                    'upgrade-details-page.disabled-modules.message'                             => static::t('upgrade-details-page.disabled-modules.message'),
                    'upgrade-details-page.disabled-modules.message.link'                        => static::t('upgrade-details-page.disabled-modules.message.link'),
                    'upgrade-details-page.backup-confirm'                                       => static::t('upgrade-details-page.backup-confirm'),
                    'upgrade-details-page.remove-confirm'                                       => static::t('upgrade-details-page.remove-confirm'),
                    'upgrade-details-page.remove-confirm.link'                                  => static::t('upgrade-details-page.remove-confirm.link'),
                    'upgrade-details-page.continue'                                             => static::t('upgrade-details-page.continue'),
                    'upgrade-details-page.licenses.updated'                                     => static::t('upgrade-details-page.licenses.updated'),
                    'upgrade.advanced_mode.message'                                             => static::t('upgrade.advanced_mode.message'),
                    'upgrade.advanced_mode.warning.message'                                     => static::t('upgrade.advanced_mode.warning.message'),
                    'upgrade.advanced_mode.select_all'                                          => static::t('upgrade.advanced_mode.select_all'),
                    'upgrade-self.title'                                                        => static::t('upgrade-self.title'),
                    'upgrade-self.description'                                                  => static::t('upgrade-self.description'),
                    'upgrade-self.install-update'                                               => static::t('upgrade-self.install-update'),
                    'module.changelog'                                                          => static::t('module.changelog'),
                    'module.upgrade.remove'                                                     => static::t('module.upgrade.remove'),
                    'upgrade-details-page.custom-modules-confirm'                               => static::t('upgrade-details-page.custom-modules-confirm'),
                    'upgrade-details-page.custom-modules-confirm.custom-modules'                => static::t('upgrade-details-page.custom-modules-confirm.custom-modules'),
                    'upgrade-details-page.custom-modules-warning'                               => static::t('upgrade-details-page.custom-modules-warning'),
                    'upgrade-details-page.custom-modules-warning.custom-modules'                => static::t('upgrade-details-page.custom-modules-warning.custom-modules'),
                    'upgrade-details-page.expired-license.license'                              => static::t('upgrade-details-page.expired-license.license'),
                    'upgrade-details-page.expired-license.message'                              => static::t('upgrade-details-page.expired-license.message'),
                    'upgrade-details-page.missing-license.message'                              => static::t('upgrade-details-page.missing-license.message'),
                    'upgrade-details-page.missing-module-license.message'                       => static::t('upgrade-details-page.missing-module-license.message'),
                    'upgrade-details-page.requirements-warning.php.message'                     => static::t('upgrade-details-page.requirements-warning.php.message'),
                    'upgrade-details-page.wave-warning.message'                                 => static::t('upgrade-details-page.wave-warning.message'),
                    'upgrade-details-page.wave-warning.message.link'                            => static::t('upgrade-details-page.wave-warning.message.link'),
                    'upgrade-details-page.wave-warning.title'                                   => static::t('upgrade-details-page.wave-warning.title'),
                    'module.upgrade.author'                                                     => static::t('module.upgrade.author'),
                    'module_action.upgrade_addon.minor'                                         => static::t('module_action.upgrade_addon.minor'),
                    'module_action.upgrade_addon.major'                                         => static::t('module_action.upgrade_addon.major'),
                    'module_state_message.update_available'                                     => static::t('module_state_message.update_available'),
                    'module_state_message.update_available.update_link'                         => static::t('module_state_message.update_available.update_link'),
                    'modules-page.marketplace.empty.message'                                    => static::t('modules-page.marketplace.empty.message'),
                    'modules-page.marketplace.empty.search-tips'                                => static::t('modules-page.marketplace.empty.search-tips'),
                    'modules-page.marketplace.empty.search-tips-2'                              => static::t('modules-page.marketplace.empty.search-tips-2'),
                    'modules-page.marketplace.empty.search-tips-2.suggest-idea'                 => static::t('modules-page.marketplace.empty.search-tips-2.suggest-idea'),
                    'modules-page.marketplace.empty.search-tips-2.contact-xcart'                => static::t('modules-page.marketplace.empty.search-tips-2.contact-xcart'),
                    'modules-page.marketplace.empty.back-link'                                  => static::t('modules-page.marketplace.empty.back-link'),
                    'header.addons_search_subtitle'                                             => static::t('header.addons_search_subtitle'),
                    'success-upgrade.title'                                                     => static::t('All done!'),
                    'success-upgrade.description'                                               => static::t('If you have any questions, get back to us via support@x-cart.com. We are available 24/7.'),
                    'success-upgrade.storefront'                                                => static::t('Open storefront'),
                    'success-upgrade.admin-area'                                                => static::t('Go to dashboard'),
                    'success-upgrade.message'                                                   => static::t('Please do not close this page until you make sure your store works properly.'),
                    'success-upgrade.installed-addon'                                           => static::t('Go to addon settings'),
                    'success-upgrade.installed-addons'                                          => static::t('Go to installed addons'),
                    'success-upgrade.installed-message'                                         => static::t('Now you can adjust the settings of the addons that have been installed.'),
                    'success-upgrade.installed-message-link'                                    => static::t('Now you can check the addons that have been installed'),
                    'success-renewal.message'                                                   => static::t('Access to updates has been renewed'),
                    'failure-renewal.message'                                                   => static::t("We haven't been able to renew access to upgrades for you. Please contact our customer support team for upgrade access renewal."),
                    'access-denied.title'                                                       => static::t('Access denied!'),
                    'access-denied.text'                                                        => static::t('You are not allowed to access this resource!'),
                    'tech-info.title'                                                           => static::t('Store information'),
                    'tech-info.store-version'                                                   => static::t('Version'),
                    'tech-info.service-version'                                                 => static::t('Service tool version'),
                    'tech-info.active-template'                                                 => static::t('Active template'),
                    'tech-info.url'                                                             => static::t('Template URL'),
                    'tech-info.license'                                                         => static::t('License'),
                    'tech-info.install-date'                                                    => static::t('Installation date'),
                    'tech-info.private-modules'                                                 => static::t('Private and custom modules'),
                    'tech-info.search-filter'                                                   => static::t('Filter'),
                    'tech-info.search-query'                                                    => static::t('Search query'),
                    'tech-info.developer'                                                       => static::t('Developer'),
                    'tech-info.module-name'                                                     => static::t('Module name'),
                    'tech-info.version'                                                         => static::t('Version'),
                    'tech-info.description'                                                     => static::t('Description'),
                    'tech-info.no-private-modules'                                              => static::t('No private or custom modules installed'),
                    'tech-info.no-private-modules-found'                                        => static::t('No matching private or custom modules'),
                    'tech-info.public-modules'                                                  => static::t('Public installed modules'),
                    'tech-info.no-public-modules'                                               => static::t('No public modules installed'),
                    'tech-info.no-public-modules-found'                                         => static::t('No matching public modules'),
                ],
            ],
        ]];
    }

    /**
     * Is used to hide 'module controls/Activate license key button' for demostore.x-cart.com
     */
    protected function isDemoMode(): bool
    {
        return (bool) (\Includes\Utils\ConfigParser::getOptions(['demo', 'demo_mode']) ?? false);
    }

    /**
     * Check widget visibility
     */
    protected function isVisible(): bool
    {
        return \XLite::isAdminZone() && Auth::getInstance()->isLogged();
    }
}
