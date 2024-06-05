<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\Coupons\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CommonError;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;


/**
 * Class AddCartCoupon
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

class AddCartCoupon extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Coupons\AddCartCoupon
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
     * @throws \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CommonError
     * @throws \RuntimeException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var \CDev\Coupons\Model\Order|\XLite\Model\cart $cart */
        $cart = $this->cartService->retrieveCart($context);

        $code = $args['code'];
        /** @var \CDev\Coupons\Model\Coupon $coupon */
        $coupon = \XLite\Core\Database::getRepo('\CDev\Coupons\Model\Coupon')
            ->findOneByCode($code);

        if (!$coupon) {
            throw new CommonError("No coupon for $code");
        }

        $error = static::checkCompatibility($cart, $coupon);

        if (empty($error)) {
            $cart->addCoupon($coupon);
        } else {
            throw new CommonError($error);
        }
        $this->cartService->updateCart($cart);

        return $this->mapper->mapToDto($cart);
    }

    /**
     * Check coupon compatibility
     *
     * @param                                         $cart
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return string
     */
    protected static function checkCompatibility($cart, $coupon)
    {
        $error = '';

        try {
            $coupon->checkUnique($cart);
            $coupon->checkCompatibility($cart);
        } catch (\CDev\Coupons\Core\CompatibilityException $exception) {
            $error = (string)\XLite\Core\Translation::lbl($exception->getMessage(), $exception->getParams());
        }

        return $error;
    }
}
