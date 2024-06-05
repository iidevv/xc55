<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActRemovePreselection\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Config extends \XLite\Core\Config
{
    protected function postProcessConfig($config)
    {
        $config = parent::postProcessConfig($config);

        if ($config
            && $config->General
            && isset($config->General->force_choose_product_options)
        ) {
            $config->General->force_choose_product_options = 'product_page';
        }

        return $config;
    }

}