<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Coupons\Mapper;

/**
 * Class Coupon
 *
 * @Decorator\Depend("CDev\Coupons")
 */
class Coupon
{
    /**
     * @param  \CDev\Coupons\Model\UsedCoupon $coupon
     *
     * @return array
     */
    public function mapCoupon($coupon)
    {
        return [
            'id'   => $coupon->getId(),
            'code' => $coupon->getCode(),
            'name' => $coupon->getPublicName(),
            'rate' => $coupon->getValue(),
            'type' => $this->mapType($coupon->getType()),
        ];
    }

    protected function mapType($type)
    {
        $map = [
            \CDev\Coupons\Model\Coupon::TYPE_PERCENT  => 'PERCENT',
            \CDev\Coupons\Model\Coupon::TYPE_ABSOLUTE => 'ABSOLUTE',
        ];

        return isset($map[$type])
            ? $map[$type]
            : null;
    }
}
