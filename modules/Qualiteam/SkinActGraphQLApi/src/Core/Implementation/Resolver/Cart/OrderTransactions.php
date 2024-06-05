<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Model\Payment\Transaction;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Order
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart
 */
class OrderTransactions implements ResolverInterface
{
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
        $order = $val->cartModel;
        return array_map(function ($item) {
            return $this->mapToArray($item);
        }, $order->getPaymentTransactions()->toArray());
    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    public function mapToArray(Transaction $transaction)
    {
        return [
            'id'            => $transaction->getPublicId(),
            'type'          => $transaction->getType(),
            'value'         => $transaction->getValue(),
            'status'        => $transaction->getStatus(),
            'human_status'  => $transaction->getReadableStatus(),
            'method'        => $transaction->getMethodName(),
            'note'          => $transaction->getNote(),
        ];
    }
}
