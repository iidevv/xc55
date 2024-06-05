<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

/**
 * Shipping status items list
 */
class Shipping extends \XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\AStatus
{
    protected function getPage()
    {
        return 'shipping';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order\Status\Shipping';
    }
}
