<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class OfferType
 * @package XcartGraphqlApi\Types\Model
 */
class OfferType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'offer',
            'description' => 'Offer model',
            'fields'      => function () {
                return [
                    //TODO Customer name
                    'id'             => Types::id(),
                    'customer_notes' => Types::string(),
                    'admin_notes'    => Types::string(),
                    'admin_notes_visible'    => Types::string(),
                    'status'         => Types::string(),
                    'status_name'    => Types::string(),
                    'product_id'     => Types::id(),
                    'product_name'   => Types::string(),
                    'product_price'  => Types::float(),
                    'offer_price'    => Types::float(),
                    'offer_amount'   => Types::int(),
                    'date'           => Types::string(),
                ];
            },
        ];
    }
}
