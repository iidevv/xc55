<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View\Product\AttributeValue\Customer;


use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 * @Extender\After("Qualiteam\SkinActColorSwatchesFeature")
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{

    protected function getSwatchLinkAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value): array
    {
        $attributes = parent::getSwatchLinkAttributes($value);
        return array_merge($attributes, ['data-attr-swatch-id' => $value->getSwatch()->getId()]);
    }

    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActImagesForColors/ImagesForColors.js';
        return $list;
    }

    public function getCSSFiles(): array
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActImagesForColors/ImagesForColors.css';
        return $list;
    }

}