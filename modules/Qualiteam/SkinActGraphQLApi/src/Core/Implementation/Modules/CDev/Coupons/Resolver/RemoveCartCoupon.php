<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Coupons\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

/**
 * Class RemoveCartCoupon
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

class RemoveCartCoupon extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Coupons\RemoveCartCoupon
{
    protected $couponMapper;

    public function __construct(Mapper\Cart $mapper, CartService $cartService)
    {
        parent::__construct($mapper, $cartService);

        // TODO Find a way to pass this in constructor for modules. Same for wishlist
        $this->couponMapper = new Modules\CDev\Coupons\Mapper\Coupon();
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
        /** @var \CDev\Coupons\Model\Order|\XLite\Model\cart $cart */
        $cart = $this->cartService->retrieveCart($context);

        /**
         * @var \CDev\Coupons\Model\UsedCoupon $coupon
         */
        foreach ($cart->getUsedCoupons()->toArray() as $coupon) {
            if ($coupon->getCode() === $args['code']) {
                $cart->removeUsedCoupon($coupon);
            }
        }

        $this->cartService->updateCart($cart);

        return $this->mapper->mapToDto($cart);
    }
}
