<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Model;

use XCart\Extender\Mapping\Extender;
use Includes\Utils\Module\Module;
use XLite;
use XLite\Model\Config;
use CDev\GoogleAnalytics\Core\GA;

/**
 * @Extender\Mixin
 *
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isGoogleAnalyticsSettings()) {
            $list[] = 'modules/CDev/GoogleAnalytics/settings.less';
        }

        return $list;
    }

    /**
     * Check if current page is page with GA settings
     *
     * @return boolean
     */
    protected function isGoogleAnalyticsSettings(): bool
    {
        /** @var XLite\Controller\Admin\Module $this */
        return XLite::getController() instanceof XLite\Controller\Admin\Module
            && $this->getModule()
            && Module::buildId('CDev', 'GoogleAnalytics') === $this->getModule();
    }

    /**
     * Get form field by option
     *
     * @param Config $option Option
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getFormFieldByOption(Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($this->isGoogleAnalyticsSettings()) {
            switch ($option->getName()) {
                case 'ga_measurement_id':
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_SHOW => [
                            'ga_code_version' => GA::CODE_VERSION_4,
                        ],
                    ];
                    break;
                case 'ga_account':
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_HIDE => [
                            'ga_code_version' => GA::CODE_VERSION_4,
                        ],
                    ];
                    break;
                case 'ga_api_secret':
                    $cell[static::SCHEMA_REQUIRED]   = true;
                    $cell[static::SCHEMA_DEPENDENCY] = [
                        static::DEPENDENCY_SHOW => [
                            'purchase_only_on_paid' => [true],
                            'ga_code_version'       => GA::CODE_VERSION_4,
                        ],
                    ];
                    break;
            }
        }

        return $cell;
    }
}
