<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class OffersFilterInputType extends InputObjectType
{
    use Types\Traits\CollectionItemFilterTrait;

    public function configure()
    {
        return [
            'name'   => 'offers_filter_input',
            'description' => 'Offers filter input',
            'fields' => $this->defineFields()
        ];
    }

    protected function defineFields()
    {
        return array_merge([], [
            'name'   => [
                'type'        => Types::string(),
                'description' => 'Name',
            ],
            'email'   => [
                'type'        => Types::string(),
                'description' => 'Email',
            ],
            'dateRangeFrom'   => [
                'type'        => Types::int(),
                'description' => 'Timestamp date range from',
            ],
            'dateRangeTo'   => [
                'type'        => Types::int(),
                'description' => 'Timestamp date range to',
            ],
            'status'   => [
                'type'        => Types::byName('offersTypeEnum'),
                'description' => 'Search products by stock status',
            ],
            'orderBy' => [
                'type'        => Types::byName('listOrderByInputType'),
                'description' => 'Order by',
            ],
        ]);
    }

    public function __construct()
    {
        $config = $this->configure();

        parent::__construct($config);
    }
}
