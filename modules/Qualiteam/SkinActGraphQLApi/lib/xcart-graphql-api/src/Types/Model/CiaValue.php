<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class CiaValue extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'ciaValue',
            'description' => 'CIA condition value',
            'fields'      => function () {
                return [
                    'code'          => Types::string(),
                    'label'         => Types::string(),
                    'description'   => Types::string(),
                ];
            },
        ];
    }
}
