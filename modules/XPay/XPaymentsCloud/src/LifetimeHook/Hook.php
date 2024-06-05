<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XPay\XPaymentsCloud\LifetimeHook;

final class Hook
{
    /**
     * We use 'XPaymentsApplePay' and etc string literals here instead of the
     * XLite\Module\XPay\XPaymentsCloud\Main::APPLE_PAY_SERVICE_NAME constants
     * because disabled module doesn't have access to its own constants.
     */
    public function onRemove(): void
    {
        $pmApplePay = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'XPaymentsApplePay']);
        if ($pmApplePay) {
            $pmApplePay->delete();
        }

        $pmGooglePay = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'XPaymentsGooglePay']);
        if ($pmGooglePay) {
            $pmGooglePay->delete();
        }
    }
}

