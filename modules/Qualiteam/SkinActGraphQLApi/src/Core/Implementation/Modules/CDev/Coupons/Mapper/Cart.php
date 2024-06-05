<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Coupons\Mapper;

use Doctrine\Common\Collections\Collection;

/**
 * Class Cart
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CDev\Coupons")
 *
 */

class Cart extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Cart
{
    /**
     * @param \CDev\Coupons\Model\Order $cart
     *
     * @return \XcartGraphqlApi\DTO\CartDTO
     */
    public function mapToDto(\XLite\Model\Cart $cart)
    {
        $dto = parent::mapToDto($cart);

        $usedCoupons = $cart->getUsedCoupons();

        $dto->coupons = $usedCoupons instanceof Collection
            ? $usedCoupons->toArray()
            : $usedCoupons;

        return $dto;
    }
}
