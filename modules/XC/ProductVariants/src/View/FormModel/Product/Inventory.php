<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Inventory extends \XLite\View\FormModel\Product\Inventory
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/ProductVariants/form_model/product/inventory/style.less'
            ]
        );
    }

    protected function defineFields()
    {
        $fields = parent::defineFields();

        if ($this->isDisplayVariantsInventorySwitcher()) {
            $fields[self::SECTION_DEFAULT]['clear_variants_inventory'] = [
                'label'     => static::t('Clear and disable inventory tracking for variants as well'),
                'type'      => 'XLite\View\FormModel\Type\SwitcherType',
                'position'  => 150,
                'show_when' => [
                    'default' => [
                        'inventory_tracking_status' => false,
                    ],
                ],
                'help'      => static::t('Product variants inventory clear help'),
            ];
        }

        return $fields;
    }

    protected function isDisplayVariantsInventorySwitcher()
    {
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);

        if ($product && $product->hasVariants()) {
            /** @var \XC\ProductVariants\Model\ProductVariant $variant */
            foreach ($product->getVariants() as $variant) {
                if (!$variant->getDefaultAmount()) {
                    return true;
                }
            }
        }

        return false;
    }
}
