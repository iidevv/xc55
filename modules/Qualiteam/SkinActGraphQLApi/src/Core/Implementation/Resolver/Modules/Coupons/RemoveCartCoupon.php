<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Coupons;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;

/**
 * Class RemoveCartCoupon
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\Coupons
 */
class RemoveCartCoupon implements ResolverInterface
{
    /**
     * @var Mapper\Cart
     */
    protected $mapper;
    /**
     * @var CartService
     */
    protected $cartService;

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
        throw new NoModule();
    }
}
