<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\AttributeValue;

/**
 * Abstract attribute value
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

abstract class AAttributeValue extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * Get surcharge absolute value
     *
     * @param string $field Field
     *
     * @return float
     */
    public function getAbsoluteValue($field)
    {
        return 0.0;
    }
}
