<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Controller\Admin;

/**
 * Sale discount products
 */
class SaleDiscountProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage sale discounts');
    }

    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID
     *
     * @return bool
     */
    public function isExcludedProductId($productId)
    {
        $saleDiscountProduct = [
            'saleDiscount'  => \XLite\Core\Request::getInstance()->sale_discount_id,
            'product' => $productId,
        ];

        return (bool)\XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscountProduct')
                ->findOneBy($saleDiscountProduct);
    }

    /**
     * @return \CDev\Sale\Model\SaleDiscount|null
     */
    public function getSaleDiscount()
    {
        $discountId = \XLite\Core\Request::getInstance()->sale_discount_id;

        return $this->executeCachedRuntime(static function () use ($discountId) {
            return \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
                ->find($discountId);
        }, ['getSaleDiscount', $discountId]);
    }
}
