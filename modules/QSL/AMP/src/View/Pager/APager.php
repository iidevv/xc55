<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Pager;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class APager extends \XLite\View\Pager\APager
{
    /**
     * Return current list name
     *
     * @return string
     */
    protected function getListName()
    {
        return static::isAMP() ? 'amp.' . parent::getListName() : parent::getListName();
    }
}
