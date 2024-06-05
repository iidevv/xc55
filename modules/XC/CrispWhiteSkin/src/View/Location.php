<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Location extends \XLite\View\Location
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (parent::isVisible() && 2 < $this->getNodeCount())
            || (
                $this->getTarget() !== \XLite::TARGET_404
                && $this->getTarget() !== 'main'
                && $this->getNodeCount() === 1
                && !$this->isCheckoutLayout()
            );
    }
}
