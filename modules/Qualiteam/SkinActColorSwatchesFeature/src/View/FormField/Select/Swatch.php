<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View\FormField\Select;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class swatch
 * @Extender\Mixin
 */
class Swatch extends \QSL\ColorSwatches\View\FormField\Select\Swatch
{
    protected function isVisible()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            return $this->getAttribute()
                && \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->isAvailable();
        }, ['isSwatchVisible', $this->getAttribute()]);
    }
}
