<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class OrdersFiltersInputType extends InputObjectType
{
    use Types\Traits\CollectionItemFilterTrait;

    public function configure()
    {
        return [
            'name'   => 'orders_filter_input',
            'description' => 'Orders filter input',
            'fields' => $this->defineFields()
        ];
    }

    protected function defineFields()
    {
        return array_merge($this->defineCommonFields(), [
            'paymentStatus'        => [
                'type'        => Types::string(),
                'description' => 'Payment Status',
            ],
            'shippingStatus'    => [
                'type'        => Types::string(),
                'description' => 'Shipping Status',
            ],
            'dateRangeFrom'   => [
                'type'        => Types::int(),
                'description' => 'Timestamp date range from',
            ],
            'dateRangeTo'   => [
                'type'        => Types::int(),
                'description' => 'Timestamp date range to',
            ],
            'mobile_tab' => [
                'type' => Types::string(),
                'description' => 'Get active or past orders'
            ]
        ]);
    }

    public function __construct()
    {
        $config = $this->configure();

        parent::__construct($config);
    }
}
