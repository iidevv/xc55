<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Model\Features\InlineEditableEntityTrait;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    use InlineEditableEntityTrait;

    public function defineEditableProperties()
    {
        return ['description', 'briefDescription'];
    }

    /**
     * Provides metadata for the property
     *
     * @param  string  $property Checked entity property
     * @return array
     */
    public function getFieldMetadata($property)
    {
        return array_merge(
            parent::getFieldMetadata($property),
            $this->getInlineEditableMetadata()
        );
    }
}
