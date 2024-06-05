<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class ChangePaymentFields implements ResolverInterface
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
        $cart = $this->cartService->retrieveCart($context);

        $this->cartService->changePaymentFields($cart, $args['fields']);

        return $this->mapper->mapToDto($cart);
    }

    /**
     * @param int $id
     *
     * @return \XLite\Model\Payment\Method|\XLite\Model\AEntity
     */
    protected function getMethod($id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find($id);
    }
}
