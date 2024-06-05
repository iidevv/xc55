<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\Controller\Admin;


use Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class OrderList extends \XLite\Controller\Admin\OrderList
{

    protected function doActionUpdateItemsList()
    {
        parent::doActionUpdateItemsList();

        $verifiedCustomer = Request::getInstance()->verifiedCustomer;

        $wasChanged = false;

        if (is_array($verifiedCustomer) && !empty($verifiedCustomer)) {

            foreach ($verifiedCustomer as $orderId => $status) {

                if ((int)$status === 0) {
                    continue;
                }

                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

                if (!$order) {
                    continue;
                }

                if ($order->getOrigProfile()) {

                    $verificationInfo = $order->getOrigProfile()->getVerificationInfo();

                    if (!$verificationInfo) {
                        $verificationInfo = new \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo();
                        Database::getEM()->persist($verificationInfo);
                        $order->getOrigProfile()->setVerificationInfo($verificationInfo);
                        $verificationInfo->setProfile($order->getOrigProfile());
                    }

                    $wasChanged = true;
                    $verificationInfo->setStatus(VerificationInfo::STATUS_VERIFIED);

                }

            }

        }

        if ($wasChanged) {
            Database::getEM()->flush();
        }


    }

}