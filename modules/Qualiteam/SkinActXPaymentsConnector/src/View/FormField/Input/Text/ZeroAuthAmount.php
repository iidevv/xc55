<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FormField\Input\Text;

use XLite\Core\Translation;
use XLite\View\FormField\Input\Text;

/**
 * Zero-dollar auth amount
 *
 */
class ZeroAuthAmount extends Text
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
            && !preg_match('/^[0-9]+(\.[0-9]{1,2}|)$/Ss', $this->getValue())
        ) {
            $result = false;
            $this->errorMessage = Translation::lbl(
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

        $rules[] = 'custom[number]';

        return $rules;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/script.js';

        return $list;
    }

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return Translation::lbl('Authorize amount for card setup');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'amount';
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 10;
    }

    /**
     * Sets shipping address phone as default for Qiwi phone number
     *
     * @return integer
     */
    protected function getDefaultValue()
    {
        return '0.00';
    }
}
