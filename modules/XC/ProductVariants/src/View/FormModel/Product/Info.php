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
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/ProductVariants/form_model/product/info/style.less'
            ]
        );
    }

    /**
     * @param array $sections
     *
     * @return array
     */
    protected function prepareFields($sections)
    {
        $result = parent::prepareFields($sections);

        $priceDescription = $this->getDataObject()->default->identity && $this->getPriceDescriptionTemplate()
            ? $this->getWidget([
                'template' => $this->getPriceDescriptionTemplate(),
            ])->getContent()
            : '';

        $result['prices_and_inventory']['price']['description'] = $priceDescription;

        return $result;
    }

    /**
     * @return string
     */
    protected function getPriceDescriptionTemplate()
    {
        /** @var \XC\ProductVariants\Model\Product $product */
        $product = $this->getProductEntity();

        if ($product && $product->hasVariants()) {
            return 'modules/XC/ProductVariants/form_model/product/info/variants_link.twig';
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getVariantsPageUrl()
    {
        $identity = $this->getDataObject()->default->identity;

        return $this->buildURL(
            'product',
            '',
            [
                'product_id' => $identity,
                'page'       => 'variants',
            ]
        );
    }

    /**
     * @return int|string
     */
    protected function getVariantsCount()
    {
        /** @var \XC\ProductVariants\Model\Product $product */
        $product = $this->getProductEntity();

        $variantsCount = '';
        if ($product && $product->isPersistent()) {
            $repo = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant');
            $variantsCount = $repo->getVariantsCountByProduct($this->getProduct());
        }

        return $variantsCount;
    }

    protected function defineFields()
    {
        $fields = parent::defineFields();

        if ($this->isDisplayVariantsInventorySwitcher()) {
            $fields['prices_and_inventory']['inventory_tracking']['fields']['clear_variants_inventory'] = [
                'label'     => static::t('Clear and disable inventory tracking for variants as well'),
                'type'      => 'XLite\View\FormModel\Type\SwitcherType',
                'position'  => 150,
                'show_when' => [
                    'prices_and_inventory' => [
                        'inventory_tracking' => [
                            'inventory_tracking' => false,
                        ],
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
