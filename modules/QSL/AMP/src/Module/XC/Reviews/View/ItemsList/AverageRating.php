<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\XC\Reviews\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class AverageRating extends \XC\Reviews\View\Customer\ProductInfo\ItemsList\AverageRating
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP()
            ? 'modules/QSL/AMP/modules/XC/Reviews/product/items_list/rating.twig'
            : parent::getDefaultTemplate();
    }
}
