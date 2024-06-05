<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Coupons\Resolver;
use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules;


/**
 * Class Coupons
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

class Coupons extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart\Coupons
{
    protected $mapper;

    /**
     * Coupons constructor.
     */
    public function __construct()
    {
        // TODO Find a way to pass this in constructor for modules. Same for wishlist
        $this->mapper = new Modules\CDev\Coupons\Mapper\Coupon();
    }

    /**
     * @param                                    $val
     * @param                                    $args
     * @param \XcartGraphqlApi\ContextInterface  $context
     * @param ResolveInfo                        $info
     *
     * @return array|mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $coupons = [];
        $couponsRaw = $val->coupons;

        if(!$couponsRaw) {
            return [];
        }
        /**
         * @var \CDev\Coupons\Model\UsedCoupon $coupon
         */
        foreach ($couponsRaw as $coupon) {
            $coupons[] = $this->mapper->mapCoupon($coupon);
        }

        return $coupons;
    }
}
