<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard;

use XCart\Extender\Mapping\Extender;

/**
 *
 * @Extender\Mixin
 */
class XLite extends \XLite
{
    public function resetCurrency()
    {
        $this->currentCurrency = null;
    }

    public function getOptions($category, $configName = null): array
    {
        // TODO checkaim
        if (is_array($category)) {
            [$category, $configName] = $category;
        }

        return \Includes\Utils\ConfigParser::getOptions([$category, $configName]) ?? [];
    }
}
