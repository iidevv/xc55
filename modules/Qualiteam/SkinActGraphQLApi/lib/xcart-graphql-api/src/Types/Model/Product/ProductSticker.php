<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use GraphQL\Type\Definition\ObjectType;
use XcartGraphqlApi\Types;

/**
 * Class ProductSticker
 * @package XcartGraphqlApi\Types\Model\Product
 */
class ProductSticker extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'        => 'productSticker',
            'description' => 'Product option',
            'fields'      => function () {
                return [
                    'name'          => Types::string(),
                    'text_color'    => Types::string(),
                    'bg_color'      => Types::string(),
                ];
            },
        ]);
    }
}
