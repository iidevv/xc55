<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ResolverFactoryInterface;
use XcartGraphqlApi\Types;

/**
 * Class QueryType
 * @package XcartGraphqlApi\Types
 */
class QueryType extends ObjectType
{
    /**
     * QueryType constructor.
     *
     * @param ResolverFactoryInterface $factory
     *
     * @throws \Exception
     */
    public function __construct(ResolverFactoryInterface $factory)
    {
        $config = [
            'name'         => 'Query',
            'fields'       => [
                'product' => [
                    'type' => Types::byName('product'),
                    'description' => 'Single product',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Product id'
                        ]
                    ]
                ],
                'products' => [
                    'type' => Types::listOf(Types::byName('product')),
                    'description' => 'Products list',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                        'filters' => [
                            'type'        => Types::byName('productsFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ]
                ],
                'orderedProducts' => [
                    'type' => Types::listOf(Types::byName('product')),
                    'description' => 'Ordered products list',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                        'filters' => [
                            'type'        => Types::byName('productsFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ]
                ],
                'category' => [
                    'type' => Types::byName('category'),
                    'description' => 'Single category',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Category id'
                        ]
                    ]
                ],
                'productVariantImage' => [
                    'type' => Types::string(),
                    'description' => 'Get image url for provided product options',
                    'args' => [
                        'productId' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Product id'
                        ],
                        'selectedOptions' => [
                            'type'        => Types::listOf(Types::byName('productSelectedOption')),
                            'description' => 'Selected options'
                        ],
                    ]
                ],
                'categories' => [
                    'type' => Types::listOf(Types::byName('category')),
                    'description' => 'Categories list',
                    'args' => [
                        // TODO Should be ListSubsetType (?)
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                        // TODO Should be ProductFilterType (?)
                        'parent_id' => [
                            'type' => Types::id(),
                            'description' => 'Parent category ID'
                        ],
                        'search' => [
                            'type' => Types::string(),
                            'description' => 'Search query string'
                        ],
                        'enabled' => [
                            'type' => Types::boolean(),
                            'description' => 'Search only enabled categories'
                        ],
                    ]
                ],
                'catalog' => [
                    'type' => Types::listOf(Types::byName('category')),
                    'description' => 'Catalog',
                ],
                'countries' => [
                    'type' => Types::listOf(Types::byName('country')),
                    'description' => 'Countries list',
                ],
                'states' => [
                    'type' => Types::listOf(Types::byName('state')),
                    'description' => 'States list',
                ],
                'currencies' => [
                    'type' => Types::listOf(Types::byName('currency')),
                    'description' => 'Currencies list',
                ],
                'banners' => [
                    'type' => Types::listOf(Types::byName('banner')),
                    'description' => 'Banners',
                ],
                'cart' => [
                    'type' => Types::byName('cart'),
                    'description' => 'Current cart',
                    'args' => []
                ],
                'user' => [
                    'type' => Types::byName('user'),
                    'description' => 'User data',
                    'args' => [
                        'id' => [
                            'type' => Types::id(),
                            'description' => 'User id (current user id is used if not provided)'
                        ]
                    ]
                ],
                'productTags' => [
                    'type' => Types::listOf(Types::byName('productTag')),
                    'description' => 'Product tags. Requires XC-ProductTags addon.'
                ],
                'customerOrders' => [
                    'type' => Types::listOf(Types::byName('order')),
                    'description' => 'Orders of the logged customer',
                    'args' => [
                        'start' => [
                            'type' => Types::int(),
                            'description' => 'First element for query',
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Limit for query',
                        ],
                        'filters' => [
                            'type'        => Types::byName('ordersFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ],
                ],
                'sellerOrders' => [
                    'type' => Types::listOf(Types::byName('order')),
                    'description' => 'Orders of the logged vendor',
                    'args' => [
                        'start' => [
                            'type' => Types::int(),
                            'description' => 'First element for query',
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Limit for query',
                        ],
                        'filters' => [
                            'type'        => Types::byName('ordersFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ],
                ],
                'brands' => [
                    'type' => Types::listOf(Types::byName('brand')),
                    'description' => 'Product brands. Requires QSL-ShopByBrand addon.'
                ],
                'vendorPlans' => [
                    'type' => Types::listOf(Types::byName('vendorPlan')),
                    'description' => 'Vendor plans'
                ],
                'vendorPlansTexts' => [
                    'type' => Types::listOf(Types::byName('vendorPlanText')),
                    'description' => 'Vendor plans texts'
                ],
                'conversations' => [
                    'type' => Types::listOf(Types::byName('conversation')),
                    'description' => 'Conversations'
                ],
                'messages' => [
                    'type' => Types::listOf(Types::byName('message')),
                    'description' => 'Order Messages',
                    'args' => [
                        'orderNumber' => [
                            'type' => Types::int(),
                            'description' => 'Order Number',
                        ]
                    ]
                ],
                'menuNotificationsVendor' => [
                    'type' => Types::byName('menuNotificationsVendor'),
                    'description' => 'Menu Notifications Vendor'
                ],
                'ciaValues' => [
                    'type' => Types::listOf(Types::byName('ciaValue')),
                    'description' => 'CIA condition values',
                ],
                'menuNotificationsCustomer' => [
                    'type' => Types::byName('menuNotificationsCustomer'),
                    'description' => 'Menu Notifications Customer'
                ],
                'questions' => [
                    'type' => Types::listOf(Types::byName('question')),
                    'description' => 'Seller Questions List',
                    'args' => [
                        'start' => [
                            'type' => Types::int(),
                            'description' => 'First element for query',
                        ],
                        'limit' => [
                            'type' => Types::int(),
                            'description' => 'Limit for query',
                        ]
                    ]
                ],
                'faq' => [
                    'type' => Types::listOf(Types::byName('faq')),
                    'description' => 'FAQ items. Requires Guru-FAQ addon.'
                ],
                'customerOffers' => [
                    'type' => Types::listOf(Types::byName('offer')),
                    'description' => 'Customer Offers list. Requires CSI-MakeAnOffer addon.',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'First element for query',
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Limit for query',
                        ],
                        'filters' => [
                            'type'        => Types::byName('offersFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ]
                ],
                'sellerOffers' => [
                    'type' => Types::listOf(Types::byName('offer')),
                    'description' => 'Seller Offers list. Requires CSI-MakeAnOffer addon.',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'First element for query',
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Limit for query',
                        ],
                        'filters' => [
                            'type'        => Types::byName('offersFiltersInput'),
                            'description' => 'Filters'
                        ],
                    ]
                ],
                'vendors' => [
                    'type' => Types::listOf(Types::byName('vendor')),
                    'description' => 'Vendors list. Requires XC-MultiVendor addon.'
                ],
                'seller' => [
                    'type' => Types::byName('seller'),
                    'description' => 'Sellers list. Requires XC-MultiVendor addon.',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Profile Id'
                        ]
                    ]
                ],
                'productAdditionalInfo' => [
                    'type' => Types::byName('productAdditionalInfo'),
                    'description' => 'Additional financial info for product page',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Product Id'
                        ]
                    ]
                ],
                'relatedProducts' => [
                    'type' => Types::listOf(Types::byName('product')),
                    'description' => 'Related products list',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Product Id'
                        ]
                    ]
                ],
                'frequentlyBoughtTogether' => [
                    'type' => Types::listOf(Types::byName('product')),
                    'description' => 'Frequently Bought Together product list',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Product Id'
                        ]
                    ]
                ],
                'wishlist' => [
                    'type' => Types::byName('wishlist'),
                    'description' => 'Wishlist. Requires QSL-MyWishlist addon.'
                ],
                'appConfig'   => [
                    'type' => Types::byName('appConfig'),
                    'description' => 'Application configuration'
                ],
                'appData'   => [
                    'type' => Types::byName('appData'),
                    'description' => 'Application common Data'
                ],
                'info'   => [
                    'type' => Types::byName('info'),
                    'description' => 'Application Info Data'
                ],
                'contactUsInfo'   => [
                    'type' => Types::byName('contactUsInfo'),
                    'description' => 'Contact Us Page Data'
                ],
                'authLinks'   => [
                    'type' => Types::byName('authLinks'),
                    'description' => 'Auth Links'
                ],
                'collection'  => [
                    'type' => Types::byName('collection'),
                    'description' => 'Object collection',
                    'args' => [
                        'type' => [
                            'type' => Types::nonNull(Types::byName('collection_type')),
                            'description' => 'Object type'
                        ],
                        // TODO Refactor filters to accept category and product params separately
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                        'filters' => [
                            'type'        => Types::byName('productsFiltersInput'),
                            'description' => 'Filters'
                        ],
                        'parent_id' => [
                            'type' => Types::id(),
                            'description' => 'Parent category ID'
                        ],
                        'search' => [
                            'type' => Types::string(),
                            'description' => 'Search query string'
                        ],
                        'enabled' => [
                            'type' => Types::boolean(),
                            'description' => 'Search only enabled categories'
                        ],
                    ]
                ],
                'tooltips' => [
                    'type' => Types::listOf(Types::byName('tooltip')),
                    'description' => 'Tooltips',
                    'args'        => [
                        'page' => [
                            'type'        => Types::string(),
                            'description' => 'Page name',
                        ],
                    ],
                ],
                'pagesUrls' => [
                    'type' => Types::listOf(Types::byName('pagesUrl')),
                    'description' => 'Admin pages urls'
                ],
                'iconLinks' => [
                    'type'        => Types::listOf(Types::byName('iconLink')),
                    'description' => 'Icon links',
                    'args'        => [
                        'product_id' => [
                            'type'        => Types::id(),
                            'description' => 'Product ID',
                        ],
                        'order_id' => [
                            'type' => Types::id(),
                            'description' => 'Order ID'
                        ]
                    ],
                ],
                'reorderedItems' => [
                    'type' => Types::listOf(Types::byName('product')),
                    'description' => 'Reorder items list',
                ],
                'specialOffer' => [
                    'type' => Types::byName('specialOffer'),
                    'description' => 'Single special offer',
                    'args' => [
                        'id' => [
                            'type' => Types::nonNull(Types::id()),
                            'description' => 'Special offer'
                        ]
                    ]
                ],
                'specialOffers' => [
                    'type' => Types::listOf(Types::byName('specialOffer')),
                    'description' => 'Special offers',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                    ]
                ],
                'bannersList' => [
                    'type' => Types::listOf(Types::byName('bannersList')),
                    'description' => 'Banners list',
                    'args' => [
                        'from' => [
                            'type' => Types::int(),
                            'description' => 'Subset from'
                        ],
                        'size' => [
                            'type' => Types::int(),
                            'description' => 'Subset size'
                        ],
                        'category_id' => [
                            'type' => Types::int(),
                            'description' => 'Category id'
                        ]
                    ],
                ],
                'dealBlock' => [
                    'type' => Types::byName('dealBlock'),
                    'description' => "Today's deal block configuration",
                ],
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) use ($factory) {
                $resolver = $factory->createForType($info->fieldName);
                return $resolver(
                    $value, $args, $context, $info
                );
            }
        ];

        parent::__construct($config);
    }
}
