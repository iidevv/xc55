<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

abstract class APaymentMethodTabs extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/payment_method_tabs.twig';
    }
}
