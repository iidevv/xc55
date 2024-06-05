<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Helper;


use Qualiteam\SkinActAftership\Utils\Slug;

class Trackit
{
    protected $order;
    protected $customShippingMethods = [];

    protected function getCustomShippingMethods(): array
    {
        return $this->customShippingMethods;
    }

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function getTrackingUrls()
    {
        $shippingMethodName = $this->order->getShippingMethodName();

        return $this->isShippingMethodCustom($shippingMethodName)
            ? $this->prepareCustomShippingMethodUrls()
            : $this->prepareDefaultShippingMethodUrls();
    }

    protected function prepareDefaultShippingMethodUrls()
    {
        $links = [];

        foreach ($this->order->getTrackingNumbers() as $number) {
            $links[] = \Includes\Utils\URLManager::getShopURL(
                \XLite\Core\Converter::buildURL(
                    'trackings',
                    [],
                    $this->prepareUrlParams($number),
                    \XLite::CART_SELF,
                    true
                )
            );
        }

        return $links;
    }

    protected function prepareUrlParams($number): array
    {
        return [
            'trackNumber' => $number->getValue(),
            'slug' => Slug::getSlugByName($number->getAftershipCourierName()),
        ];
    }

    protected function isShippingMethodCustom(string $shippingMethodName): bool
    {
        return array_key_exists($shippingMethodName, $this->getCustomShippingMethods());
    }

    protected function prepareCustomShippingMethodUrls()
    {
        $customShippingMethods = $this->getCustomShippingMethods();
        $shippingMethodName = $this->order->getShippingMethodName();

        $links = [];

        foreach ($this->order->getTrackingNumbers() as $number) {
            $links[] = $customShippingMethods[$shippingMethodName] . $number->getValue();

        }

        return $links;
    }


}