<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

class PaymentMethods implements ResolverInterface
{
    /**
     * @var CartService
     */
    private $cartService;
    /**
     * @var Mapper\PaymentMethod
     */
    private $mapper;

    /**
     * ShippingMethods constructor.
     *
     * @param Mapper\PaymentMethod $mapper
     * @param CartService           $cartService
     */
    public function __construct(Mapper\PaymentMethod $mapper, CartService $cartService)
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
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $methods = $this->cartService->getCartPaymentMethods($val->cartModel);

        return array_map(
            function ($method) {
                return $this->mapper->mapMethod($method);
            },
            $methods
        );
    }
}
