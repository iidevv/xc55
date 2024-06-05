<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

class ShippingMethods implements ResolverInterface
{
    /**
     * @var CartService
     */
    private $cartService;
    /**
     * @var Mapper\ShippingMethodRate
     */
    private $mapper;

    /**
     * ShippingMethods constructor.
     *
     * @param Mapper\ShippingMethodRate $mapper
     * @param CartService               $cartService
     */
    public function __construct(Mapper\ShippingMethodRate $mapper, CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->mapper = $mapper;
    }

    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $cart = $val->cartModel;
        $rates = $this->cartService->getCartShippingRates($cart);

        return array_map(
            function ($rate) use ($cart) {
                return $this->mapRate($cart, $rate);
            },
            $rates
        );
    }

    /**
     * @param \XLite\Model\Cart          $cart
     * @param \XLite\Model\Shipping\Rate $rate
     *
     * @return array
     */
    protected function mapRate(\XLite\Model\Cart $cart, \XLite\Model\Shipping\Rate $rate)
    {
        $mapped = $this->mapper->mapMethodRate($rate);
        $mapped['rate'] = $this->cartService->roundPriceForCart(
            $cart,
            $mapped['rate']
        );

        return $mapped;
    }
}
