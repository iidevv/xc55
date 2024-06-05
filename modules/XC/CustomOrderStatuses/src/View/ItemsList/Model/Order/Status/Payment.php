<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

/**
 * Payment status items list
 */
class Payment extends \XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\AStatus
{
    protected function getPage()
    {
        return 'payment';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order\Status\Payment';
    }
}
