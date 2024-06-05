<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class ProductType
 * @package XcartGraphqlApi\Types\Model
 */
class ProductType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'product',
            'description' => 'Product model',
            'interfaces'  => [
                Types::byName('collection_item'),
            ],
            'fields'      => function () {
                return [
                    'id'                     => Types::id(),
                    'product_code'           => Types::string(),
                    'product_name'           => Types::string(),
                    'makeAnOfferState'       => Types::boolean(),
                    'short_description'      => Types::string(),
                    'short_description_html' => Types::string(),
                    'description'            => Types::string(),
                    'description_html'       => Types::string(),
                    'small_image_url'        => Types::string(),
                    'image_url'              => Types::string(),
                    'images'                 => Types::listOf(Types::string()),
                    'product_url'            => Types::string(),
                    'weight'                 => Types::float(),
                    'weight_u'               => Types::string(),
                    'inventory_enabled'      => Types::boolean(),
                    'amount'                 => Types::int(),
                    'price'                  => Types::float(),
                    'display_price'          => Types::float(),
                    'review_rate'            => Types::float(),
                    'reviews'                => Types::listOf(Types::byName('review')),
                    'questions'              => Types::listOf(Types::byName('question')),
                    'votes_count'            => Types::int(),
                    'enabled'                => Types::boolean(),
                    'available'              => Types::boolean(),
                    'coming_soon'            => Types::boolean(),
                    'expected_date'          => Types::string(),
                    'attributes'             => [
                        'type'    => Types::listOf(Types::byName('productAttributeGroup')),
                        'resolve' => $this->createResolveForType('productAttributes'),
                    ],
                    'options'                => [
                        'type'    => Types::listOf(Types::byName('productOption')),
                        'resolve' => $this->createResolveForType('productOptions'),
                    ],
                    'specification' => [
                        'type'    => Types::listOf(Types::byName('productSpecificationGroup')),
                        'resolve' => $this->createResolveForType('productSpecification'),
                    ],                    
                    'categories'             => Types::listOf(Types::byName('category')),

                    // TODO Module data (?)
                    'on_sale'                => Types::boolean(),
                    'sale_value'             => Types::float(),
                    'sale_type'              => Types::string(),
                    'bookable'               => Types::boolean(),
                    'is_wishlisted'          => Types::boolean(),
                    'stickers'               => Types::listOf(Types::byName('productSticker')),
                    'tags'                   => Types::listOf(Types::byName('productTag')),
                    'brand'                  => Types::byName('brand'),
                    'vendor'                 => Types::byName('vendor'),

                    'condition'              => Types::string(),
                    'conditionCode'          => Types::string(),
                    'showFreeShippingLabel'  => Types::boolean(),
                    'marketPrice'            => Types::float(),
                    'freeShippingForProMember'   => Types::boolean(),
                    'newArrival'   => Types::boolean(),

                    // product info shipping section
                    'shippingSection'        => [
                        'type' => Types::byName('productShippingSection'),
                        'resolve' => $this->createResolveForType('productShippingSection'),
                    ],
                    'unreadQuestions' => Types::int(),
                    'colorSwatches'                  => Types::listOf(Types::byName('colorSwatches')),

                    'video_tabs_info' => Types::listOf(Types::byName('videoTours')),

                    'reorder_attributes' => Types::listOf(Types::byName('reorder_attributes')),
                    'review_list_url' => Types::string(),
                    'video_tour_url' => Types::string(),
                ];
            },
        ];
    }
}
