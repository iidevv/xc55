<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\LifetimeHook;

use CDev\USPS\Main;

class Hook
{
    public function onInit(): void
    {
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'CDev\USPS\Model\Shipping\Processor\USPS'
        );

        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'CDev\USPS\Model\Shipping\Processor\PB'
        );
    }

    public function onUpgradeTo5503(): void
    {
        $yamlFile = LC_DIR_MODULES . 'CDev/USPS/resources/hooks/upgrade/5.5/0.3/upgrade.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }

        $isLiveMode = false;

        if (
            !empty(\XLite\Core\Config::getInstance()->CDev->USPS->server_url)
            && \XLite\Core\Config::getInstance()->CDev->USPS->server_url === Main::getUrlLive()
        ) {
            $isLiveMode = true;
        }

        if ($isLiveMode) {
            $testMode = \XLite\Core\Database::getRepo('XLite\Model\Config')
                ->findBy(['name' => 'CDev\USPS', 'category' => 'test_mode']);

            if ($testMode) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                    $testMode,
                    ['value' => false]
                );
            }
        }

        $serverUrl = \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findBy(['name' => 'CDev\USPS', 'category' => 'server_url']);

        if ($serverUrl) {
            \XLite\Core\Database::getEM()->remove($serverUrl);
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
