<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;
use XLite\View\AView;

/**
 * Subscription orders page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SubscriptionOrders extends AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'x_payments_subscription';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription_orders/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && Request::getInstance()->subscription_id;
    }
}
