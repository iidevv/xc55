<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\StickyPanel\Order\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order info sticky panel
 * @Extender\Mixin
 */
class Info extends \XLite\View\StickyPanel\Order\Admin\Info
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if ($this->getOrder()->isNotFinishedOrder()) {
            $list['sendNotification'] = $this->getDoNotSendNotificationWidget();
        }

        return $list;
    }
}
