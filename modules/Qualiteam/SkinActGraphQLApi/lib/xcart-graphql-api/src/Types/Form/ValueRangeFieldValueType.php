<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Form;

use XcartGraphqlApi\Types;
use GraphQL\Type\Definition\ObjectType;

/**
 * Class ValueRangeFieldValueType
 * @package XcartGraphqlApi\Types\Form
 */
class ValueRangeFieldValueType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name'        => 'value_range_field_value',
            'description' => 'Form Field model',
            'fields'      => function () {
                return [
                    'from'  => Types::float(),
                    'to'    => Types::float(),
                ];
            },
        ]);
    }
}
