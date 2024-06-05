<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class BannerHtmlType extends ObjectType
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        return [
            'name'        => 'bannerHtml',
            'description' => 'Banner html model',
            'fields'      => function () {
                return [
                    'content' => Types::string(),
                ];
            },
        ];
    }
}