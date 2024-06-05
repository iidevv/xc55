<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Module\XC\MultiVendor\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XC\MultiVendor\Main;
use XC\MultiVendor\Model\Commission;

/**
 * FinancialInfo
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class FinancialInfo extends \XC\MultiVendor\View\Model\Profile\FinancialInfo
{
    /**
     * @return array
     */
    protected function defineTaxCalculationSchema()
    {
        $schema = parent::defineTaxCalculationSchema();

        if ($this->isDisplayUsStatesForTaxField()) {
            $schema += [
                'us_tax_calculate_for' => [
                    self::SCHEMA_CLASS => 'XC\AvaTax\View\FormField\Select\AutomaticTaxCalculateType',
                    self::SCHEMA_LABEL => 'Automatically calculate taxes for',
                ],
                'us_tax_states' => [
                    self::SCHEMA_CLASS => '\XC\AvaTax\View\FormField\Select\Select2\StateCodes',
                    self::SCHEMA_LABEL => static::t('Specify US states'),
                    self::SCHEMA_DEPENDENCY => [
                        self::DEPENDENCY_SHOW => [
                            'us_tax_calculate_for' => [\XC\AvaTax\View\FormField\Select\AutomaticTaxCalculateType::PARAM_SPECIFIC_STATES],
                        ],
                    ],
                ],
            ];
        }

        return $schema;
    }

    /**
     * @return bool
     */
    protected function isDisplayUsStatesForTaxField()
    {
        return !\XC\MultiVendor\Main::isWarehouseMode()
            && Config::getInstance()->XC->MultiVendor->taxes_owner === Commission::TAXES_OWNER_VENDOR;
    }

    public function getDefaultFieldValue($name)
    {
        switch ($name) {
            case 'us_tax_calculate_for':
                $value = Main::getVendorConfiguration(
                    $this->getModelObject(),
                    [
                        'XC',
                        'MultiVendor',
                    ]
                )->us_tax_calculate_for;
                break;
            case 'us_tax_states':
                $value = Main::getVendorConfiguration(
                    $this->getModelObject(),
                    [
                        'XC',
                        'MultiVendor',
                    ]
                )->us_tax_states;
                break;
            default:
                $value = parent::getDefaultFieldValue($name);
                break;
        }

        return $value;
    }
}
