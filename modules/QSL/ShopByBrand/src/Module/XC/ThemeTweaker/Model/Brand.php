<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Module\XC\ThemeTweaker\Model;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Model\Features\InlineEditableEntityTrait;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class Brand extends \QSL\ShopByBrand\Model\Brand
{
    use InlineEditableEntityTrait;

    /**
     * @return array
     */
    public function defineEditableProperties()
    {
        return ['description'];
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
