<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View;

use XCart\Extender\Mapping\ListChild;
use Includes\Utils\Module\Manager;
use CDev\GoogleAnalytics\Core\GA;

/**
 * Tabber link widget
 *
 * @ListChild (list="tabs.content", zone="admin")
 */
class TabberLink extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['orders_stats']);
    }

    /**
     * Check if the Google Analitics module is configured
     *
     * @return boolean
     */
    protected function isConfigured(): bool
    {
        return GA::getResource()->isConfigured();
    }

    /**
     * Defines the module link to configure
     *
     * @return string
     */
    protected function getModuleLink(): string
    {
        return Manager::getRegistry()->getModuleSettingsUrl('CDev', 'GoogleAnalytics');
    }

    /**
     * Return widget default template
     *
     * @return string
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/tabs/link.twig';
    }
}
