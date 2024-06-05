<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Check - product has Description tab or not
     *
     * @return boolean
     */
    protected function hasDescription()
    {
        return \XLite::getController()->hasDescription()
            || 0 < $this->getProduct()->getAttachments()->count();
    }
}
