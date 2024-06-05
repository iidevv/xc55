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
class InfoType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'info',
            'description' => 'Application info data',
            'fields'      => function () {
                return [
                    'shipping'                  => Types::string(),
                    'contacts'                  => Types::string(),
                    'terms_and_conditions'      => Types::string(),
                    'privacy_policy'            => Types::string(),
                ];
            },
        ];
    }
}
