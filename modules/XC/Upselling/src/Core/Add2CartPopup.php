<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\Add2CartPopup")
 */
class Add2CartPopup extends \XC\Add2CartPopup\Core\Add2CartPopup
{
    /**
     * Get products list
     *
     * @param integer $productId  Current product ID
     * @param array   $productIds Product ID which must be excluded from the search result
     * @param integer $maxCount   Maximum number of products
     *
     * @return array
     */
    public function getSourceRelatedProducts($productId, $productIds, $maxCount)
    {
        $result = [];

        $cnd = new \XLite\Core\CommonCell();
        if ($productIds) {
            $cnd->{\XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_EXCL_PRODUCT_ID} = $productIds;
        }
        $cnd->{\XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_PARENT_PRODUCT_ID} = $productId;
        $cnd->{\XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_DATE} = \XLite\Core\Converter::getDayEnd(\XLite\Base\SuperClass::getUserTime());
        $cnd->{\XC\Upselling\Model\Repo\UpsellingProduct::P_LIMIT} = [0, $maxCount + 1];

        $products = \XLite\Core\Database::getRepo('XC\Upselling\Model\UpsellingProduct')
            ->search($cnd, false);

        foreach ($products as $product) {
            $result[] = $product->getProduct();
        }

        return $result;
    }

    /**
     * Register products source 'Related Products' for 'Add to Cart popup' module
     *
     * @return array
     */
    protected function getSources()
    {
        $sources = parent::getSources();
        $sources['REL'] = [
            'method' => 'getSourceRelatedProducts',
            'position' => 100,
        ];

        return $sources;
    }
}
