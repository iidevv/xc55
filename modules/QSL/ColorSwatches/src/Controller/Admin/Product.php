<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Update product Color Swatch Settings
     */
    protected function doActionUpdateColorSwatchesSettings()
    {
        $data = \XLite\Core\Request::getInstance()->data;

        if (!$data) {
            return;
        }

        /** @var \XLite\Model\Repo\Attribute $repo */
        $repo = \XLite\Core\Database::getRepo('\XLite\Model\Attribute');
        $product = $this->getProduct();

        foreach ($data as $aid => $properties) {
            /** @var \XLite\Model\Attribute $attribute */
            $attribute = $repo->find($aid);
            if ($attribute) {
                $attribute->setShowSelector(['product' => $product, 'show_selector' => $properties['show_selector']]);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     *
     * @return bool
     */
    public function isColorSwatchesAvailable($attribute)
    {
        return ($attribute
            && $attribute->getProduct()
            && $attribute->getType() == \XLite\Model\Attribute::TYPE_SELECT
            && $attribute->getDisplayMode() == \XLite\Model\Attribute::COLOR_SWATCHES_MODE)
            || $attribute === null;
    }
}
