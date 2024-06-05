<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

use Includes\Utils\ConfigParser;
use XCart\Domain\ModuleManagerDomain;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\MarketplaceClient;

/**
 * Migration Logic - Missing modules
 */
class MissingModules extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    /**
     * Step title
     *
     * @return string
     */
    public function getStepTitle()
    {
        return \XLite\Core\Translation::lbl(
            'Missing modules',
            [
                'Action' => \XLite\Core\Translation::lbl(
                    $this->hasNotInstalledModules() ? 'Install' : 'Enable'
                )
            ]
        );
    }

    /**
     * Step title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->hasNotInstalledModules()
            ? \XLite\Core\Translation::lbl('Install and continue')
            : \XLite\Core\Translation::lbl('Enable and continue');
    }

    /**
     * @return array
     */
    public static function getMissingModulesIds()
    {
        $modulesList = [];

        if (($step = \XLite::getController()->getWizard()->getStep('DetectTransferableData'))) {
            $rules = $step->getSelectedRules();

            foreach ($rules as $rule) {
                if (($modules = $rule::getNotInstalledModules()) && !empty($modules)) {
                    $modulesList += $modules;
                }
                if (($modules = $rule::getDisabledModules()) && !empty($modules)) {
                    $modulesList += $modules;
                }
            }
        }

        return $modulesList;
    }

    /**
     * List of modules required
     *
     * @return array
     */
    public static function getMissingModules(): array
    {
        static $compatibleMarketplaceModules;

        if (
            !isset($compatibleMarketplaceModules)
            && ($moduleIds = static::getMissingModulesIds())
        ) {
            $compatibleMarketplaceModules = static::getCompatibleMarketplaceModules($moduleIds);
        }

        return $compatibleMarketplaceModules ?? [];
    }

    public static function hasNotInstalledModules()
    {
        if (($step = \XLite::getController()->getWizard()->getStep('DetectTransferableData'))) {
            $rules = $step->getSelectedRules();

            foreach ($rules as $rule) {
                if (($modules = $rule::getNotInstalledModules()) && !empty($modules)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-' . (static::hasNotInstalledModules() ? 'Install' : 'Enable');
    }

    private static function getCompatibleMarketplaceModules(array $filterByIds): array
    {
        if (!$filterByIds) {
            return [];
        }

        $allMarketPlaceModules = ExecuteCached::executeCachedRuntime(static function () {
            $allMarketPlaceModules = MarketplaceClient::getInstance()->retrieve('get_addons', ['autoResolveParams' => ['modules']]);

            return !empty($allMarketPlaceModules['modules']) ? $allMarketPlaceModules['modules'] : [];
        }, [__CLASS__, __METHOD__]) ?: [];

        // filter all possible addons by requested
        $xlite           = \XLite::getInstance();
        $filteredModules = [];
        foreach ($filterByIds as $moduleServiceName) {
            [$filterAuthor, $filterName] = explode('\\', $moduleServiceName);
            foreach ($allMarketPlaceModules as $possibleModule) {
                if (
                    $filterAuthor == $possibleModule['author']
                    && $filterName == $possibleModule['name']
                    && $xlite->getMajorVersion() == $possibleModule['version']['major']
                    && version_compare($xlite->getMinorOnlyVersion(), $possibleModule['minorRequiredCoreVersion'], '>=')
                ) {
                    $filteredModules[] = $possibleModule;
                    break;
                }
            }
        }

        // change format to modules/XC/MigrationWizard/templates/web/admin/modules/XC/MigrationWizard/actions/missing_modules.twig
        $moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
        $addonImagesUrl      = ConfigParser::getOptions(['marketplace', 'addon_images_url']);
        foreach ($filteredModules as $module) {
            [$author, $moduleName] = explode('-', "{$module['author']}-{$module['name']}");
            $installedModule = $moduleManagerDomain->getModule("{$author}-{$moduleName}");

            $result[] = [
                'icon'       => "{$addonImagesUrl}{$author}/{$moduleName}/icon.png",
                'moduleName' => $module['readableName'],
                'id'         => "{$author}-{$moduleName}",
                'installed'  => !empty($installedModule),
            ];
        }

        return $result ?? [];
    }
}
