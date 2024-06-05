<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class CollectionItemType
 * @package XcartGraphqlApi\Types
 */
class CollectionItemType extends InterfaceType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'collection_item',
            'description' => 'Object as a part of collection',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                ];
            },
        ];
    }
}
