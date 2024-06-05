<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CartServiceException;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class ChangeShipping implements ResolverInterface
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

        $shippingId = $args['shipping_id'];
        $method = $this->getMethod($shippingId);
        if (!$method) {
            throw new CartServiceException("No {$shippingId} method was found");
        }

        $this->cartService->changeShippingMethod($cart, $method);

        if ($cart->getShippingId() !== (int)$shippingId) {
            throw new CartServiceException("Can't change shipping method to {$shippingId}");
        }

        return $this->mapper->mapToDto(
            $cart
        );
    }

    /**
     * @param int $id
     *
     * @return \XLite\Model\Shipping\Method|\XLite\Model\AEntity
     */
    protected function getMethod($id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')
            ->find($id);
    }
}
