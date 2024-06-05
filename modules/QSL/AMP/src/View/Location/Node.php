<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Location;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Node extends \XLite\View\Location\Node
{
    /**
     * Get link URL
     *
     * @return string
     */
    protected function getLink()
    {
        return static::isAMP() ? $this->getAbsoluteURL(parent::getLink()) : parent::getLink();
    }
}
