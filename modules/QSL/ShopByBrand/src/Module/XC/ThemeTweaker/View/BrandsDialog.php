<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Module\XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
abstract class BrandsDialog extends \QSL\ShopByBrand\View\BrandsDialog implements ThemeTweaker\View\LayoutBlockInterface, \XLite\Base\IDecorator
{
    use ThemeTweaker\View\LayoutBlockTrait;

    protected function getDefaultDisplayName()
    {
        return static::t('Shop by brand');
    }
}
