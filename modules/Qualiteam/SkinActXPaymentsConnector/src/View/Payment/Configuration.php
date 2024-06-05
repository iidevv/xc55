<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * Payment configuration page
 *
 * @Extender\Mixin
 */
abstract class Configuration extends \XLite\View\Payment\Configuration
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/payment/style.css';

        return $list;
    }
}
