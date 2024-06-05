<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class BannerImagesType extends ObjectType
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        return [
            'name'        => 'bannerImages',
            'description' => 'Banner image model',
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