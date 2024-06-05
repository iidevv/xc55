<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\ItemsList\Model;

class PaypalButtonPFM extends \CDev\Paypal\View\ItemsList\Model\PaypalButton
{
    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        $data = parent::getPlainData();

        return [
            static::TYPE_CHECKOUT => $data[static::TYPE_CHECKOUT],
        ];
    }
}
