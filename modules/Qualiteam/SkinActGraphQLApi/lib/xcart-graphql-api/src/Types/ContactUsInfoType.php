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
class ContactUsInfoType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'contact_us_info',
            'description' => 'Contact us info data',
            'fields'      => function () {
                return [
                    'about_us_content'  => Types::string(),
                    'showrooms_content' => Types::string(),
                    'showrooms_info'    => Types::listOf(Types::byName('showroomInfo')),
                    'about_us_title' => Types::string(),
                    'about_us_subtitle' => Types::string(),
                    'about_us_working_days' => Types::string(),
                    'about_us_working_hours' => Types::string(),
                    'toll_free_phone' => Types::string(),
                    'international_phone' => Types::string(),
                    'fax' => Types::string(),
                ];
            },
        ];
    }
}
