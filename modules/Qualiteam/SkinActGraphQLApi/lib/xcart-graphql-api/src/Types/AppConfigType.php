<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class AppConfigType
 * @package XcartGraphqlApi\Types
 */
class AppConfigType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'app_config',
            'description' => 'Application configuration',
            'fields'      => function () {
                return [
                    'store_platform'       => Types::string(),
                    'store_version'        => Types::string(),
                    'currency'             => Types::byName('currency'),
                    'default_language'     => Types::byName('language'),
                    'default_country'      => Types::byName('country'),
                    'date_format'          => Types::string(),
                    'time_format'          => Types::string(),
                    'contact_email'        => Types::string(),
                    'contact_phone'        => Types::string(),
                    'contact_address'      => Types::string(),
                    'contact_fax'          => Types::string(),
                    'terms_and_conditions' => Types::string(),
                    'is_webview_checkout_flow' => Types::boolean(),
                    'email_as_login'       => Types::boolean(),
                ];
            },
        ];
    }
}
