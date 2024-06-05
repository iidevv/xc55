<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Location\Node;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Home extends \XLite\View\Location\Node\Home
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/location/home.twig' : 'location/home.twig';
    }

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
