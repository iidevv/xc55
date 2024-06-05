<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class TransactionType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'transaction',
            'description' => 'Order transaction',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'type'          => Types::string(),
                    'value'         => Types::string(),
                    'status'        => Types::string(),
                    'human_status'  => Types::string(),
                    'method'        => Types::string(),
                    'note'          => Types::string(),
                ];
            },
        ];
    }
}
