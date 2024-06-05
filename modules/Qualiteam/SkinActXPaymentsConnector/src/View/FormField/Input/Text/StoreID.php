<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FormField\Input\Text;

use XLite\View\FormField\Input\Text;

/**
 * Store ID 
 *
 */
class StoreID extends Text
{
    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if (
            $result
            && $this->getValue()
            && !preg_match('/^[0-9a-f]{32}$/Ss', $this->getValue())
        ) {
            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field has an incorrect format',
                array(
                    'name' => $this->getLabel(),
                )
            );
        }

        return $result;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'custom[onlyLetterNumber]';

        return $rules;
    }
}
