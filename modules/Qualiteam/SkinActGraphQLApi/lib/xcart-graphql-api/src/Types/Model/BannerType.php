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
 * Class Banner
 * @package XcartGraphqlApi\Types\Model
 */
class BannerType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'banner',
            'description' => 'Banner model',
            'fields'      => function () {
                return [
                    'id'        => Types::id(),
                    'image_url' => Types::string(),
                    'type'      => Types::string(),
                    'data'      => Types::string(),
                ];
            },
        ];
    }
}
