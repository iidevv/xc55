<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

use XLite\View\FormField\Select\AbsoluteOrPercent;

trait CarriersTrait
{
    /**
     * Check if method is offline
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isOffline($method)
    {
        return $method->getProcessor() === 'offline';
    }

    /**
     * Returns handling fee as a string
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getHandlingFee($method)
    {
        $handlingFeeValue = $method->getHandlingFeeValue();

        if ($method->getHandlingFeeType() === AbsoluteOrPercent::TYPE_PERCENT) {
            $handlingFee = $handlingFeeValue . AbsoluteOrPercent::getInstance()->getPercentTypeLabel();
        } else {
            $currency    = \XLite::getInstance()->getCurrency();
            $handlingFee = $currency->getPrefix() . $currency->formatValue($handlingFeeValue) . $currency->getSuffix();
        }

        return $handlingFee;
    }

    /**
     * Returns tax class name
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getTaxClassName($method)
    {
        /** @var \XLite\Model\TaxClass $taxClass */
        $taxClass = $method->getTaxClass();

        return $taxClass
            ? $taxClass->getName()
            : 'Default';
    }

    /**
     * Returns method settings URL
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getSettingsURL(\XLite\Model\Shipping\Method $method)
    {
        return $method->getProcessorObject()
            ? $method->getProcessorObject()->getSettingsURL()
            : '';
    }

    /**
     * Check if method is configured or not
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isConfigured(\XLite\Model\Shipping\Method $method)
    {
        return in_array($method->getProcessor(), ['shipping_solution', 'offline'])
            || (
                $method->getProcessorObject()
                && $method->getProcessorObject()->isConfigured()
            );
    }
}
