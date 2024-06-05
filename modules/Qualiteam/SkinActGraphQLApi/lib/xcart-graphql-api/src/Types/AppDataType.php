<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class AppDataType
 * @package XcartGraphqlApi\Types
 */
class AppDataType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'app_data',
            'description' => 'Application common data',
            'fields'      => function () {
                return [
                    'currencies'              => Types::listOf(Types::byName('currency')),
                    'languages'               => Types::listOf(Types::byName('language')),
                    'countries'               => Types::listOf(Types::byName('country')),
                    'states'                  => Types::listOf(Types::byName('state')),
                    'profile_fields'          => Types::listOf(Types::byName('profileField')),
                    'memberships'             => Types::listOf(Types::byName('membership')),
                    'modules'                 => Types::listOf(Types::byName('module')),
                    'home_page_widgets'       => Types::listOf(Types::byName('homePageWidget')),
                    'external_auth_providers' => Types::listOf(Types::byName('authProvider')),
                    'mobileAppCategories'              => Types::listOf(Types::byName('category')),
                    'proMembershipProduct'              => Types::listOf(Types::byName('product')),
                    'savedCardsUrl'              => Types::string(),
                    'departmentsList'            => Types::listOf(Types::string()),
                    'request_catalog_url'        => Types::string(),
                    'request_catalog_image_url'  => Types::string(),
                    'google_map_api_key'         => Types::string(),
                    'cloud_search_api_key'       => Types::string(),

                    // TODO Should it be?
                    // 'custom_states'        => Types::listOf(Types::string()),
                ];
            },
        ];
    }
}
