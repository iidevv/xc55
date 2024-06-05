<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\AppData;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 *
 */
class AuthProvider extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'authProvider',
            'description' => 'External OAuth2-compatible authorization provider',
            'fields'      => function () {
                return [
                    'display_name' => [
                        'type' => Types::string(),
                        'description' => 'Auth provider display name'
                    ],
                    'service_name' => [
                        'type' => Types::string(),
                        'description' => 'Auth provider service name'
                    ],
                    'authorize_url' => [
                        'type' => Types::string(),
                        'description' => 'Authorize grant URL. Open that in WebView to allow user to pass the grant and code acquiring process.'
                    ]
                ];
            },
        ];
    }
}
