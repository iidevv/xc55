<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\View\Model;

/**
 * Test shipping rates widget
 */
class TestRates extends \XLite\View\Model\TestRates
{
    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XC\UPS\View\Form\TestRates';
    }

    /**
     * Get list of available schema fields
     *
     * @return array
     */
    protected function getAvailableSchemaFields()
    {
        return [
            static::SCHEMA_FIELD_WEIGHT,
            static::SCHEMA_FIELD_SUBTOTAL,
            static::SCHEMA_FIELD_SRC_CITY,
            static::SCHEMA_FIELD_SRC_COUNTRY,
            static::SCHEMA_FIELD_SRC_STATE,
            static::SCHEMA_FIELD_SRC_CUSTOM_STATE,
            static::SCHEMA_FIELD_SRC_ZIPCODE,
            static::SCHEMA_FIELD_DST_CITY,
            static::SCHEMA_FIELD_DST_COUNTRY,
            static::SCHEMA_FIELD_DST_STATE,
            static::SCHEMA_FIELD_DST_CUSTOM_STATE,
            static::SCHEMA_FIELD_DST_ZIPCODE,
            static::SCHEMA_FIELD_DST_TYPE,
            static::SCHEMA_FIELD_COD_ENABLED,
        ];
    }
}
