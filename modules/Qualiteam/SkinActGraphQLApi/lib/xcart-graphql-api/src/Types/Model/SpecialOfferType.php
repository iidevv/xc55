<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class SpecialOfferType extends ObjectType
{

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        return [
            'name'        => 'specialOffer',
            'description' => 'special offer model',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'title' => Types::string(),
                    'image_url' => Types::string(),
                    'description' => Types::string(),
                    'short_promo_text' => Types::string(),
                ];
            },
        ];
    }
}