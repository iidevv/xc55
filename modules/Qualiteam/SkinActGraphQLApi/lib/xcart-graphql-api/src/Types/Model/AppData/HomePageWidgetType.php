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
 * Class ProductSelectionType
 */
class HomePageWidgetType extends ObjectType
{
    const TYPE_PRODUCT_LIST = 'product_list';

    const TYPE_CATEGORY_LIST = 'category_list';

    public function configure()
    {
        return [
            'name'        => 'homePageWidget',
            'description' => 'Home page widgets information',
            'fields'      => function () {
                return [
                    'display_name' => Types::string(),
                    'service_name' => Types::string(),
                    'enabled'      => Types::boolean(),
                    'type'         => Types::string(),
                    'categories'   => Types::listOf(Types::byName('category')),
                    'params'       => Types::byName('widgetParams'),
                ];
            },
        ];
    }
}
