<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Module\XC\MultiVendor\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * FinancialInfo
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class FinancialInfo extends \XC\MultiVendor\Controller\Admin\FinancialInfo
{
    /**
     * @throws \Exception
     */
    protected function doActionModify()
    {
        parent::doActionModify();

        $this->updateEnabledUsTaxStates();
    }

    /**
     * @throws \Exception
     */
    protected function updateEnabledUsTaxStates()
    {
        $calculateFor = \XLite\Core\Request::getInstance()->us_tax_calculate_for;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'vendor' => $this->getProfile(),
            'category' => 'XC\\MultiVendor',
            'name' => 'us_tax_calculate_for',
            'value' => $calculateFor ?: \XC\AvaTax\View\FormField\Select\AutomaticTaxCalculateType::PARAM_ALL_STATES
        ]);

        $states = \XLite\Core\Request::getInstance()->us_tax_states;

        if (!empty($states) && is_array($states)) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'vendor' => $this->getProfile(),
                'category' => 'XC\\MultiVendor',
                'name' => 'us_tax_states',
                'value' => @serialize(array_map('mb_strtoupper', $states))
            ]);
        } else {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'vendor' => $this->getProfile(),
                'category' => 'XC\\MultiVendor',
                'name' => 'us_tax_states',
                'value' => ''
            ]);
        }
    }
}
