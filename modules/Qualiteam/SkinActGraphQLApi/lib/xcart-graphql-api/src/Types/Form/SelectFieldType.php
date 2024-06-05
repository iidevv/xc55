<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Form;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class SelectFieldType
 * @package XcartGraphqlApi\Types\Form
 */
class SelectFieldType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'select_field',
            'description' => 'Select Field model',
            'fields'      => function () {
                return [
                    'name'      => Types::string(),
                    'label'     => Types::string(),
                    'type'      => Types::byName('formFieldTypeEnum'),
                    'defaultValue' => Types::string(),
                    'required'  => Types::boolean(),
                    'options'   => Types::listOf(Types::byName('selectFieldValue')),
                    'multiple'  => Types::boolean(),
                ];
            },
        ];
    }
}
