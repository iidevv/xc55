<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * Import form widget
 * @Extender\Mixin
 */
class Import extends \XLite\View\Form\Import
{
    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return $this->getTarget() == \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY
            ? \XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY
            : parent::getDefaultTarget();
    }
}
