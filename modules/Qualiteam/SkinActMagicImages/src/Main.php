<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages;

/**
 * Magic360 module main class
 *
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Get module path
     *
     * @return string
     */
    public static function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActMagicImages';
    }
}
