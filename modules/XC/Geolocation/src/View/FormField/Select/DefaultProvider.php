<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\View\FormField\Select;

/**
 * Catalog extraction type selector
 */
class DefaultProvider extends \XLite\View\FormField\Select\Regular
{
    protected $providers;

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        if (!$this->providers) {
            $classes = \XC\Geolocation\Logic\Geolocation::getInstance()->getProviders();
            $providers = [];
            if ($classes) {
                foreach ($classes as $class) {
                    $provider = new $class();
                    $providers[$class] = $provider->getProviderName();
                }
            }
            $this->providers = $providers;
        }
        return $this->providers;
    }
}
