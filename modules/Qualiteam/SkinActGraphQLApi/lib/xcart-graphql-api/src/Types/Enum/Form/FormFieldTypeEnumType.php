<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum\Form;

use GraphQL\Type\Definition\EnumType;

/**
 * Class FormFieldTypeEnumType
 * @package XcartGraphqlApi\Types\Enum
 */
class FormFieldTypeEnumType extends EnumType
{
    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'name'        => 'form_field_type',
            'description' => 'Products filter view type enumeration',
            'values'      => [
                'FIELD_TYPE_TEXT'           => 'FIELD_TYPE_TEXT',
                'FIELD_TYPE_EMAIL'          => 'FIELD_TYPE_EMAIL',
                'FIELD_TYPE_PHONE'          => 'FIELD_TYPE_PHONE',
                'FIELD_TYPE_SEARCH'         => 'FIELD_TYPE_SEARCH',
                'FIELD_TYPE_SELECT'         => 'FIELD_TYPE_SELECT',
                'FIELD_TYPE_VALUE_RANGE'    => 'FIELD_TYPE_VALUE_RANGE',
                'FIELD_TYPE_DATE'           => 'FIELD_TYPE_DATE',
                'FIELD_TYPE_DATE_RANGE'     => 'FIELD_TYPE_DATE_RANGE',
                'FIELD_TYPE_SWITCH'         => 'FIELD_TYPE_SWITCH',
                'FIELD_TYPE_QUANTITY'       => 'FIELD_TYPE_QUANTITY',
            ]
        ]);
    }
}
