<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Controller\Admin;

/**
 * Gift tier products
 */
class GiftTierProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @return bool
     */
    public function isExcludedProductId($productId)
    {
        return (bool)\XLite\Core\Database::getRepo('Qualiteam\SkinActFreeGifts\Model\FreeGiftItem')->findOneBy([
            'product'  => $productId
        ]);
    }
}
