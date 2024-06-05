<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\Button;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use Qualiteam\SkinActAftership\Traits\TrackitTrait;
use Qualiteam\SkinActAftership\Utils\Slug;

/**
 * Class popup button TrackIt
 */
class Trackit extends \XLite\View\Button\APopupButton
{
    use AftershipTrait;
    use TrackitTrait;

    /** @var string */
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
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/button/popup/trackit.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles(): array
    {
        $list                         = parent::getCommonFiles();
        $list[static::RESOURCE_CSS][] = $this->getModulePath() . '/button/trackit.less';

        return $list;
    }

    public function getWarningText(): string
    {
        return $this->isCurrentSlugCarrier('customco')
            ? static::t('SkinActAftership please select or create and select customco shipping method for checking a tracking number')
            : static::t('SkinActAftership please select or create and select roadrunner shipping method for checking a tracking number');
    }

    protected function isCurrentSlugCarrier($value): bool
    {
        return str_contains($this->getSlug(), $value);
    }

    protected function getSlug(): ?string
    {
        return $this->getItem()
            ? $this->prepareSlugName(
                $this->getItem()->getAftershipCourierName()
            ) : null;
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

    public function getCSSFiles()
    {
        $list[] = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/button/popup/style.less';

        return $list;
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
     * @inheritDoc
     */
    protected function prepareURLParams(): array
    {
        $params   = [];
        $tracking = $this->getItem();

        if ($tracking) {
            $params = $this->prepareParams();
        }

        return $params;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareParams(): array
    {
        $params = [
            'data-shippingmethod' => $this->getOrderShippingMethodName(),
            'data-tracknumber'    => $this->getItem()->getValue(),
            'data-slug'           => $this->getSlug(),
        ];

        return array_merge($this->addCustomParams(), $params);
    }

    /**
     * Added a custom params url
     *
     * @return array
     */
    protected function addCustomParams(): array
    {
        $params             = [];

        if ($this->isSlugCustomCarrier()) {
            $params['data-customurl'] = $this->getCustomCarrierUrl();
        }

        return $params;
    }

    protected function getCustomCarrierUrl()
    {
        return $this->isCurrentSlugCarrier('customco')
            ? $this->prepareCurrentShippingMethodUrl('CustomCo')
            : $this->prepareCurrentShippingMethodUrl('Road Runner Freight');
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getButtonAttributes(): array
    {
        $params   = [];
        $tracking = $this->getItem();

        if ($tracking) {
            $params = $this->prepareParams();
        }

        return parent::getButtonAttributes() + $params;
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass(): string
    {
        return trim(parent::getClass() . ' popup-trackit hidden');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return static::t('SkinActAftership track it button');
    }

    /**
     * Default withoutClose value
     *
     * @return boolean
     */
    protected function getDefaultWithoutCloseState(): bool
    {
        return true;
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/order/popup_button.twig';
    }

    protected function isSlugCustomCarrier(): bool
    {
        return $this->isCurrentSlugCarrier('customco')
            || $this->isCurrentSlugCarrier('roadrunner');
    }
}