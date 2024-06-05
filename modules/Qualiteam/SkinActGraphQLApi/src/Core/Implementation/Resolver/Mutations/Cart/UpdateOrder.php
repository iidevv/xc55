<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Order;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CommonError;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class UpdateOrder implements ResolverInterface
{
    /**
     * @var \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Order
     */
    protected $mapper;

    /**
     * CustomerOrders constructor.
     * @param Order $mapper
     */
    public function __construct(Order $mapper)
    {
        $this->mapper = $mapper;
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
        /** @var Order $order */
        $order = $this->getOrder($args['order_id']);

        if (!$order) {
            throw new CommonError('Order not found');
        }

        if (!empty($args['shippingStatus'])) {
            $order->setShippingStatus($args['shippingStatus']);
        }

        if (!empty($args['paymentStatus'])) {
            $order->setPaymentStatus($args['paymentStatus']);
        }

        if ($order->update()) {
            return $this->mapper->mapToDto($order);
        }

        throw new CommonError('Error while updating order');
    }

    /**
     * @param $order_id
     * @return \XLite\Model\AEntity|null
     */
    protected function getOrder($order_id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->find($order_id);
    }
}
