<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\ItemsList\Model\ProductVariant;

/**
 * AttributesNames
 */
class AttributesNames extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductVariants/variants/parts/variants/attributes_names.twig';
    }

    /**
     * @return array
     */
    protected function getNames()
    {
        $result = [];

        foreach ($this->getVariantsAttributes() as $attribute) {
            $result[] = $attribute->getName();
        }

        return $result;
    }
}
