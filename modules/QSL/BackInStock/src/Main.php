<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock;

use XLite\Core\Skin;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Determines if skin module is active
     *
     * @param string $name
     *
     * @return boolean
     */
    public static function isCurrentSkin($name)
    {
        return Skin::getInstance()->getCurrentSkinModuleId() === $name;
    }
}
