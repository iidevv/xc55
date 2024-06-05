<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class BannersListType extends ObjectType
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        return [
            'name' => 'bannersList',
            'description' => 'Banners list',
            'fields' => function () {
                return [
                    'categories' => Types::listOf(Types::byName('category')),
                    'linksList' => Types::listOf(Types::byName('appBannerImages')),
                    'banner_position' => Types::int(),
                ];
            },
        ];
    }
}