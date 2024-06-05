<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Logic\ProductFeed\Step;

use XCart\Extender\Mapping\Extender;
use XC\FacebookMarketing\Core\ProductFeedDataExtractor;
use XC\FacebookMarketing\Core\ProductFeedDataWriter;

/**
 * Products
 *
 * @Extender\Mixin
 * @Extender\Depend({"XC\ProductVariants", "XC\GoogleFeed"})
 */
class AllProductsVariants extends \XC\FacebookMarketing\Logic\ProductFeed\Step\AllProducts
{
    /**
     * @inheritdoc
     *
     * @param $model \XLite\Model\Product
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        if ($model->hasVariants()) {
            $isProductOutOfStock = $model->getAmount() <= 0 && $model->getInventoryEnabled();

            foreach ($model->getVariants() as $variant) {
                $isVariantOutOfStock = $variant->getDefaultAmount()
                    ? $isProductOutOfStock
                    : $variant->getAmount() <= 0;

                $shouldSkipEntity = \XLite\Core\Config::getInstance()->XC->FacebookMarketing->include_out_of_stock === 'N'
                    && $isVariantOutOfStock;

                if (!$shouldSkipEntity) {
                    $extractor = new ProductFeedDataExtractor($this->getProductFeed());
                    $extractor->extractEntityData($variant);

                    ProductFeedDataWriter::getInstance()->writeFeedData($extractor);
                }
            }
        } else {
            parent::processModel($model);
        }
    }
}
