<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Payment;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class MethodStatus extends \XLite\View\Payment\MethodStatus
{
    /**
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return Request::getInstance()->target === 'paypal_credit' && Request::getInstance()->method_id
            ? static::getDefaultPaymentMethod()
            : parent::getPaymentMethod();
    }

    protected function hasObsoleteIPNcallback(): bool
    {
        if ($this->getPaymentMethod()->getServiceName() !== \CDev\Paypal\Main::PP_METHOD_PCP) {
            return false;
        }
        $tenDays         = 86400 * 10;
        $obsoleteIPNtime = \XLite\Core\Database::getRepo(\XLite\Model\TmpVar::class)->getVar('obsoleteIPNcallbackDetected');
        if (
            !empty($obsoleteIPNtime)
            && $obsoleteIPNtime + $tenDays < \LC_START_TIME
        ) {
            \XLite\Core\Database::getRepo(\XLite\Model\TmpVar::class)->removeVar('obsoleteIPNcallbackDetected');
            $obsoleteIPNtime = 0;
        }

        return !empty($obsoleteIPNtime);
    }
}
