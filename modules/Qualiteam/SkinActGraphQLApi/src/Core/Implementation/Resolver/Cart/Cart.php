<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use Doctrine\ORM\ORMException;
use GraphQL\Type\Definition\ResolveInfo;
use Throwable;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

class Cart implements ResolverInterface
{
    /**
     * @var Mapper\Cart
     */
    private $mapper;
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * Product constructor.
     *
     * @param Mapper\Cart $mapper
     */
    public function __construct(Mapper\Cart $mapper, CartService $cartService)
    {
        $this->mapper = $mapper;
        $this->cartService = $cartService;
    }

    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        try {
            $cart = $this->cartService->retrieveCart($context);

            return $this->mapper->mapToDto($cart);

        } catch(Throwable $e) {
            throw new \RuntimeException("Internal error occured while trying to find cart");
        }
    }
}
