<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\CloudSearch\View\CloudFilters;

use XCart\Extender\Mapping\Extender;

/**
 * Cloud filters sidebar box widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\CloudSearch")
 */
class FiltersBox extends \QSL\CloudSearch\View\CloudFilters\FiltersBox
{
    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('[SkinActSkin] Shopping Options');
    }
}
