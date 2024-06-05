<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

use XLite\View\FormField\Select\Select2Trait;
use CDev\Paypal;

/**
 * Disabled funding methods
 */
class DisabledFundingMethods extends \XLite\View\FormField\Select\Multiple
{
    use Select2Trait {
        Select2Trait::getValueContainerClass as getSelect2ValueContainerClass;
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = $this->getSelect2ValueContainerClass();

        $class .= ' input-disabled-funding-methods-select2';

        return $class;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/disabled_funding_methods.js';

        return $list;
    }

    /**
     * @return mixed
     */
    protected function getPlaceholderLabel()
    {
        return static::t('Disabled funding methods');
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);

        return json_decode(
            $paymentMethod->getSetting('fundingMethods'),
            true
        );
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_string($value)) {
            $value = json_decode($value);
        }

        parent::setValue($value);
    }

    /**
     * @param array $value
     *
     * @return string
     */
    public function prepareRequestData($value)
    {
        return json_encode($value);
    }
}
