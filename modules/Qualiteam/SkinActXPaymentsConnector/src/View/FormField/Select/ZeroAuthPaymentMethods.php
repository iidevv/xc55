<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FormField\Select;

use Qualiteam\SkinActXPaymentsConnector\Core\ZeroAuth;
use XLite\Core\Translation;
use XLite\View\FormField\Select\Regular;

/**
 * Selector for zero-dollar authorization payment method
 */
class ZeroAuthPaymentMethods extends Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = array(
            ZeroAuth::DISABLED => Translation::lbl('Do not use card setup'),
        );

        $result += ZeroAuth::getInstance()->getCanSaveCardsMethods(true);

        return $result;
    }

    /**
     * Get field label
     *
     * @return string
     */
    public function getLabel()
    {
        return Translation::lbl('Payment method for card setup');
    }

    /**
     * Get default name
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return 'method_id';
    }

}
