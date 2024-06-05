<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use XCart\Extender\Mapping\Extender;

/**
 * Return to the store in X-Payments's iframe
 *
 * @Extender\Mixin
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
{
    /**
     * Return
     *
     * @return void
     */
    protected function doActionReturn()
    {
        $txn = $this->detectTransaction();

        if ($txn && $txn->isXpc()) {
            XPaymentsClient::getInstance()->fixSavedCardMethod();
        }

        $this->getIframe()->enable();

        parent::doActionReturn();
    }
}
