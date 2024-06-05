<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

use XCart\Extender\Mapping\ListChild;

/**
 * Activate license key popup text
 *
 * @ListChild (list="marketplace.addons-filters", weight="300", zone="admin")
 */
class ActivateLicenseKey extends \XLite\View\Button\ActivateKey
{
    protected function getDefaultLabel(): string
    {
        return 'Activate license key';
    }

    protected function isModuleActivation(): bool
    {
        return true;
    }
}
