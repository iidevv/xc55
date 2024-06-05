<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use XcartGraphqlApi\DTO\CartDTO;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class Payment implements ResolverInterface
{
    /**
     * @var Mapper\PaymentMethod
     */
    private $mapper;

    /**
     * ShippingMethods constructor.
     *
     * @param Mapper\PaymentMethod $mapper
     */
    public function __construct(Mapper\PaymentMethod $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var \XLite\Model\Payment\Method $payment */
        /** @var CartDTO $val */
        $payment = $val->payment;

        if (!$payment) {
            return null;
        }

        return $this->mapper->mapMethod($payment, $val->cartModel);
    }
}
