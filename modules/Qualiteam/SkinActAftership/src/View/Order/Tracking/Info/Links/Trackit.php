<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\Order\Tracking\Info\Links;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use Qualiteam\SkinActAftership\Traits\TrackitTrait;
use Qualiteam\SkinActAftership\Utils\Slug;

class Trackit extends \XLite\View\AView
{
    use AftershipTrait;
    use TrackitTrait;

    const PARAM_ITEM = 'item';

    /**
     * @var array
     */
    protected array $customShippingMethods = [];

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->customShippingMethods = $this->getDefaultCustomMethods();
    }

    /**
     * Get custom shipping methods
     *
     * @return array
     */
    public function getCustomShippingMethods(): array
    {
        return $this->customShippingMethods;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += $this->addWidgetParamTrackingNumberItem();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/order_tracking_information/parts/trackit_link.twig';
    }

    protected function isCurrentSlugCarrier($value): bool
    {
        return str_contains($this->getSlug(), $value);
    }

    /**
     * @return string|null
     */
    protected function getSlug(): ?string
    {
        return $this->getItem()
            ? $this->prepareSlugName(
                $this->getItem()->getAftershipCourierName()
            ) : null;
    }

    /**
     * Get location url
     *
     * @return string
     */
    public function getLocationUrl(): string
    {
        return $this->isSlugCustomCarrier()
            ? $this->getCustomCarrierUrl()
            : $this->prepareDefaultUrl();
    }

    protected function isSlugCustomCarrier(): bool
    {
        return $this->isCurrentSlugCarrier('customco')
            || $this->isCurrentSlugCarrier('roadrunner');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible(): bool
    {
        return parent::isVisible()
            && Slug::getSlugByName($this->getItem()->getAftershipCourierName());
    }

    /**
     * Prepare slug name
     *
     * @param string $name
     *
     * @return string
     */
    protected function prepareSlugName(string $name): string
    {
        return Slug::getSlugByName($name);
    }

    protected function getCustomCarrierUrl()
    {
        return $this->isCurrentSlugCarrier('customco')
            ? $this->prepareCurrentShippingMethodUrl('CustomCo')
            : $this->prepareCurrentShippingMethodUrl('Road Runner Freight');
    }

    /**
     * Prepare url params
     *
     * @return array
     */
    protected function prepareUrlParams(): array
    {
        return [
            'trackNumber' => $this->getItem()->getValue(),
            'slug' => Slug::getSlugByName($this->getItem()->getAftershipCourierName()),
        ];
    }

    /**
     * Prepare default url
     *
     * @return string
     */
    protected function prepareDefaultUrl(): string
    {
        return \Includes\Utils\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL(
                'trackings',
                [],
                $this->prepareUrlParams(),
                \XLite::CART_SELF,
                true
            )
        );
    }
}