<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Labels extends \XLite\View\Labels
{
    /**
     * Get name of the working directory
     *
     * @return string
     */
    protected function getDir()
    {
        return static::isAMP() ? 'modules/QSL/AMP/labels' : parent::getDir();
    }

    /**
     * Return widget template
     *
     * @return string

    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }*/
}
