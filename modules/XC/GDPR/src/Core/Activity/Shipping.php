<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Core\Activity;

use XLite\Core\Database;
use XLite\Model\Shipping\Processor\Offline;
use XC\GDPR\Model\Activity as ActivityModel;

class Shipping extends Common
{
    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return ActivityModel
     */
    public static function update(\XLite\Model\Shipping\Method $method)
    {
        if (static::isShippingSuitable($method)) {
            $item = static::getItemByShipping($method);
            $type = ActivityModel::TYPE_SHIPPING;

            if (!($activity = static::findByItemAndType($item, $type))) {
                $activity = static::createByItemAndType($item, $type);
                Database::getEM()->persist($activity);
            }

            $activity->setDetails(array_merge($activity->getDetails(), [
                'name'   => $method->getName(),
                'module' => $method->getModuleName(),
            ]));

            return $activity;
        }

        return null;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return bool
     */
    protected static function isShippingSuitable(\XLite\Model\Shipping\Method $method)
    {
        return $method->isAdded()
            && $method->isEnabled()
            && $method->getProcessorObject()
            && !$method->getProcessorObject() instanceof Offline;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return string
     */
    protected static function getItemByShipping(\XLite\Model\Shipping\Method $method)
    {
        $method = $method->getParentMethod() ?: $method;

        return $method->getModuleName()
            ?: $method->getProcessor()
                ?: $method->getCode();
    }
}
