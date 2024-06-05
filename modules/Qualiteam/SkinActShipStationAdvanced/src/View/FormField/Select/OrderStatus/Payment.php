<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\View\FormField\Select\OrderStatus;

class Payment extends \XLite\View\FormField\Select\OrderStatus\Payment
{
    protected function getOptions()
    {
        $list = parent::getOptions();

        unset($list[0]);

        return $list;
    }
}
