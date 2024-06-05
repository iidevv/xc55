<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class CollectionType
 * @package XcartGraphqlApi\Types
 */
class CollectionType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'collection',
            'description' => 'Object collection',
            'fields'      => function () {
                return [
                    'count'       => [
                        'type' => Types::int(),
                    ],
                    'objects'     => [
                        'type' => Types::listOf(Types::byName('collection_item')),
                    ]
                ];
            },
        ];
    }
}
