<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Model;

/**
 * TestRates widget
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
        return 'XC\CanadaPost\View\Form\TestRates';
    }

    /**
     * Returns the list of related targets
     *
     * @return array
     */
    protected function getAvailableSchemaFields()
    {
        return [
            static::SCHEMA_FIELD_WEIGHT,
            static::SCHEMA_FIELD_SUBTOTAL,
            static::SCHEMA_FIELD_SRC_COUNTRY,
            static::SCHEMA_FIELD_SRC_ZIPCODE,
            static::SCHEMA_FIELD_DST_COUNTRY,
            static::SCHEMA_FIELD_DST_ZIPCODE,
        ];
    }

    /**
     * Alter the default field set
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $result = parent::getTestRatesSchema();
        $result[static::SCHEMA_FIELD_SRC_COUNTRY][static::SCHEMA_CLASS] = 'XLite\View\FormField\Input\Text';
        $result[static::SCHEMA_FIELD_SRC_COUNTRY][static::SCHEMA_ATTRIBUTES] = ['readonly' => 'readonly'];

        return $result;
    }

    /**
     * Alter default model object values
     *
     * @return array
     */
    protected function getDefaultModelObjectValues()
    {
        $data = parent::getDefaultModelObjectValues();
        $data[static::SCHEMA_FIELD_SRC_COUNTRY] = 'Canada';

        return $data;
    }
}
