<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Presenter;

use XCart\Container;
use XLite\Model\Order;

class JSYotpoConversionTracking
{
    /**
     * @param \XLite\Model\Order $order
     *
     * @return string
     */
    public function getYotpoConversionTrackingScript(Order $order): string
    {
        $appKey = Container::getContainer()?->get('yotpo.reviews.configuration')?->getAppKey();
        $orderNumber = $order->getOrderNumber();
        $orderAmount = $order->getTotal();
        $orderCurrency = $order->getCurrency()->getCode();

        return sprintf(
            '<script type="text/javascript">(function e(){var e=document.createElement("script");e.type="text/javascript",e.async=true,e.src="//staticw2.yotpo.com/%s/widget.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})();</script><script>yotpoTrackConversionData = {orderId: "%s", orderAmount: "%s", orderCurrency: "%s"}</script><noscript><img src="//api.yotpo.com/conversion_tracking.gif?app_key=%s&order_id=%s&order_amount=%s&order_currency=%s" width="1" height="1"></noscript>',
            $appKey,
            $orderNumber,
            $orderAmount,
            $orderCurrency,
            $appKey,
            $orderNumber,
            $orderAmount,
            $orderCurrency
        );
    }
}