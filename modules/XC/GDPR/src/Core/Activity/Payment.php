<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\Activity;

use XLite\Core\Database;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\Processor\Offline;
use XC\GDPR\Model\Activity as ActivityModel;

class Payment extends Common
{
    /**
     * @param Method $method
     *
     * @return ActivityModel
     */
    public static function update(Method $method)
    {
        if (static::isPaymentSuitable($method)) {
            $item = static::getItemByPayment($method);
            $type = ActivityModel::TYPE_PAYMENT;

            if (!($activity = static::findByItemAndType($item, $type))) {
                $activity = static::createByItemAndType($item, $type);
                Database::getEM()->persist($activity);
            }

            $activity->setDetails(array_merge($activity->getDetails(), [
                'name' => $method->getName(),
            ]));

            return $activity;
        }

        return null;
    }

    /**
     * @param Method $method
     *
     * @return bool
     */
    protected static function isPaymentSuitable(Method $method)
    {
        return !$method->getProcessor() instanceof Offline
            && $method->isEnabled()
            && $method->getAdded();
    }

    /**
     * @param Method $method
     *
     * @return string
     */
    protected static function getItemByPayment(Method $method)
    {
        return $method->getServiceName();
    }
}
