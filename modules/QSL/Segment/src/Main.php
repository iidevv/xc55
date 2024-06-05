<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * @return boolean
     */
    public static function hasGdprRelatedActivity()
    {
        return true;
    }
}
