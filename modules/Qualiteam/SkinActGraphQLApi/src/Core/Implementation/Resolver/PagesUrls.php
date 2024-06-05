<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XLite\Core\Converter;

class PagesUrls implements \XcartGraphqlApi\Resolver\ResolverInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $pages = [
            'profile_transactions'  => 'profile_transactions',
            'shipping_methods'      => 'shipping_methods',
            //'saved_cards'           => 'xpayments_cards',
        ];

        $urls  = [];
        foreach ($pages as $key => $page) {
            $urls[] = [
                'url'  => Converter::buildFullURL($page, '', [
                    'vendor_product_editor' => 1,
                ], \XLite::getAdminScript()),
                'page' => $key,
            ];
        }

        $urls[] = [
            'url'  => Converter::buildFullURL('xpayments_cards', '', [
            ], \XLite::getCustomerScript()),
            'page' => 'saved_cards',
        ];

        return $urls;
    }
}