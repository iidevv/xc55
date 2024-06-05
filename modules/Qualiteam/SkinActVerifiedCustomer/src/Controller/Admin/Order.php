<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\Controller\Admin;


class Order extends \XLite\Controller\Admin\Order
{
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

//        $order = $this->getOrder();
//
//        $sid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActVerifiedCustomer->order_verified_status_id;
//
//        if ($order
//            &&  $order->getShippingStatus()
//            &&$order->getShippingStatus()->getId() === $sid)
//        if($order->getOrigProfile()->getVerificationInfo())



    }

}