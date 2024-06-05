<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * \XLite\View\Model\Profile\AdminMain
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    public const SECTION_AVATAX = 'avatax';

    /**
     * AvaTax schema
     *
     * @var   array
     */
    protected $avataxSchema = [
        'avaTaxExemptionNumber' => [
            self::SCHEMA_CLASS    => '\XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Exemption number',
            self::SCHEMA_MODEL_ATTRIBUTES => [
                \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
            ],
        ],
        'avaTaxCustomerUsageType' => [
            self::SCHEMA_CLASS    => '\XC\AvaTax\View\FormField\Select\CustomerUsageTypes',
            self::SCHEMA_LABEL    => 'Usage type',
        ],
    ];

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getProfileMainSections()
    {
        return parent::getProfileMainSections()
            + [
                static::SECTION_AVATAX => 'AvaTax settings',
            ];
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionAvatax()
    {
        return $this->getFieldsBySchema($this->avataxSchema);
    }
}
