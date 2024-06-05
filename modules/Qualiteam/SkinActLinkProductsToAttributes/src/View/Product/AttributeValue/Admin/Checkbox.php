<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\AttributeValue\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Checkbox extends \XLite\View\Product\AttributeValue\Admin\Checkbox
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/manage_attribute_value/checkbox/body.twig';
    }
}