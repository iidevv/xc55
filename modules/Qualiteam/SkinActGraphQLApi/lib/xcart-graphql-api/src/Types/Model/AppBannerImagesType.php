<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class AppBannerImagesType extends ObjectType
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        return [
            'name'        => 'appBannerImages',
            'description' => 'App Banner image model',
            'fields'      => function () {
                return [
                    'image_url' => Types::string(),
                    'banner_url' => Types::string(),
                ];
            },
        ];
    }
}