<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Traits;

use XLite\Core\Config;
use XLite\Model\OrderTrackingNumber;

/**
 * TrackIt trait
 */
trait TrackitTrait
{
    /**
     * Get default custom methods
     *
     * @return array
     */
    public function getDefaultCustomMethods(): array
    {
        return [
            'CustomCo'            => Config::getInstance()->Qualiteam->SkinActAftership->cm_customco,
            'Road Runner Freight' => Config::getInstance()->Qualiteam->SkinActAftership->cm_roadrunnerfreight,
        ];
    }

    /**
     * Add widget param tracking number item
     *
     * @return \XLite\Model\WidgetParam\TypeObject[]
     */
    public function addWidgetParamTrackingNumberItem(): array
    {
        return [
            static::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Tracking number',
                null,
                false,
                OrderTrackingNumber::class
            ),
        ];
    }

    /**
     * Get tracking number item object
     *
     * @return OrderTrackingNumber|null
     */
    public function getItem(): ?object
    {
        return $this->getParam(static::PARAM_ITEM);
    }

    /**
     * Get order shipping method name
     *
     * @return string
     */
    protected function getOrderShippingMethodName(): string
    {
        return $this->getItem()->getOrder()->getShippingMethodName() ?? '';
    }

    /**
     * Get custom shipping method url
     *
     * @return string
     */
    protected function prepareCustomShippingMethodUrl(): string
    {
        $customShippingMethods = $this->getCustomShippingMethods();
        $shippingMethodName    = $this->getOrderShippingMethodName();

        return $customShippingMethods[$shippingMethodName] . $this->getItem()->getValue();
    }

    /**
     * Is current shipping method is "CustomCo" or "Road Runner Freight"
     *
     * @param string $shippingMethodName
     *
     * @return bool
     */
    protected function isShippingMethodCustom(string $shippingMethodName): bool
    {
        return array_key_exists($shippingMethodName, $this->getCustomShippingMethods());
    }

    protected function prepareCurrentShippingMethodUrl($value): string
    {
        $customShippingMethods = $this->getCustomShippingMethods();

        return $customShippingMethods[$value] . $this->getItem()->getValue();
    }
}