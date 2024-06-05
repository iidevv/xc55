<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AttrSwatch extends \QSL\ColorSwatches\View\FormField\Select\Swatch
{
    protected function getSwatches()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(static function () {
            $qb = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')->createQueryBuilder('s');
            $qb->orderBy('translations.name', 'asc');
            return $qb->getResult();
        }, ['all_active_swatches']);
    }

}