<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum;

use GraphQL\Type\Definition\EnumType;

class OfferStatusEnumType extends EnumType
{
    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        $config = [
            'name'        => 'offer_status_type',
            'description' => 'Offer status enumeration',
            'values'      => [
                'ACCEPTED'            => [
                    'value'       => 'A',
                    'description' => 'Accepted'
                ],
                'DECLINED'   => [
                    'value'       => 'D',
                    'description' => 'Declined'
                ],
                'PENDING'  => [
                    'value'       => 'P',
                    'description' => 'Pending'
                ],
                'CLOSED'  => [
                    'value'       => 'C',
                    'description' => 'Closed'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
