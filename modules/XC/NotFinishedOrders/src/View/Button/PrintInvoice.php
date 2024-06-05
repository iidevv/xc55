<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * Test shipping rates widget
 * @Extender\Mixin
 */
class PrintInvoice extends \XLite\View\Button\PrintInvoice
{
    /**
     * Return URL params to use with onclick event
     *
     * @return array
     */
    protected function getURLParams()
    {
        $result = parent::getURLParams();

        if ($this->getOrder()->isNotFinishedOrder() && isset($result['url_params'])) {
            $result['url_params']['order_id'] = $this->getOrder()->getOrderId();
            unset($result['url_params']['order_number']);
        }

        return $result;
    }
}
