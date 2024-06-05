<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use CDev\GoogleAnalytics\Core\GA\JsList\Dummy;
use XLite;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\URLManager;
use XLite\Core\WidgetDataTransport;
use CDev\GoogleAnalytics\Core\GA\Interfaces\{ILibrary, IResource};
use CDev\GoogleAnalytics\View\Header\AHeaderTag;
use XLite\View\AView;
use XLite\View\Controller;

abstract class ALibrary implements ILibrary
{
    use ExecuteCachedTrait;

    /**
     * @var IResource
     */
    protected $resource;

    /**
     * @var AView
     */
    protected $viewer;

    public function __construct(IResource $resource)
    {
        $this->resource = $resource;

        $this->viewer = XLite::getController()->getViewer();
        $this->viewer->init();
    }

    /**
     * @param       $method
     * @param array $args
     *
     * @return null
     */
    public function __call($method, array $args)
    {
        return null;
    }

    public function getTrackingType(): int
    {
        return $this->resource->getTrackingType();
    }

    public function getTagContent(): string
    {
        if ($w = $this->getTagWidget()) {
            return $w->getContent() ?? '';
        }

        return '';
    }

    protected function getTagWidget()
    {
        $params = $this->getFinalTagWidgetParams();

        return $this->getWidget(static::tagWidgetClass(), $params);
    }

    protected function getFinalTagWidgetParams(): array
    {
        return array_merge_recursive($this->getTagWidgetParams(), $this->resource->getTagWidgetParams());
    }

    protected function getTagWidgetParams(): array
    {
        return [
            AHeaderTag::PARAM_MEASUREMENT_ID    => $this->getMeasurementId(),
            AHeaderTag::PARAM_SCRIPT_URL        => $this->getScriptUrl(),
            AHeaderTag::PARAM_JS_SETTINGS       => $this->getJSSettings(),
            AHeaderTag::PARAM_ECOMMERCE_ENABLED => $this->isEcommerceEnabled(),
            AHeaderTag::PARAM_DEBUG_ENABLED     => $this->isDebugMode(),
            AHeaderTag::PARAM_TRACKER_CONFIG    => $this->getTrackerConfig(),
            AHeaderTag::PARAM_DISABLE_TRACKING  => $this->isTrackingDisabled(),
        ];
    }

    public function getMeasurementId(): string
    {
        return $this->resource->getMeasurementId();
    }

    protected function getJSSettings(): array
    {
        return [
            'measurementId' => $this->getMeasurementId(),
            'sendPageview'  => $this->isSendPageviewActive(),
            'currency'      => $this->getCurrencyCode(),
        ];
    }

    public function isSendPageviewActive(): bool
    {
        return !XLite::isAdminZone();
    }

    public function getCurrencyCode(): string
    {
        return $this->resource->getCurrencyCode();
    }

    public function isEcommerceEnabled(): bool
    {
        return $this->resource->isECommerceEnabled();
    }

    public function isDebugMode(): bool
    {
        return $this->resource->isDebugMode();
    }

    protected function getTrackerConfig(): array
    {
        return [];
    }

    public function isTrackingDisabled(): bool
    {
        return false;
    }

    /**
     * @param string $class  Widget class name
     * @param array  $params Widget params OPTIONAL
     *
     * @return WidgetDataTransport|AView Note: Union with AView because WidgetDataTransport kind of decorator without interface
     * @noinspection PhpReturnDocTypeMismatchInspection
     */
    protected function getWidget(string $class, array $params = [])
    {
        $handler = class_exists($class)
            ? $this->getViewer()->getWidget($params, $class)
            : null;

        return new WidgetDataTransport($handler);
    }

    /**
     * @return Controller
     */
    protected function getViewer(): AView
    {
        return $this->viewer;
    }

    abstract protected static function tagWidgetClass(): string;

    public function getJsList(): AJsList
    {
        if ($this->isTagActive()) {
            $class = static::jsListClass();
            return new $class($this);
        }

        return new Dummy($this);
    }

    abstract protected static function jsListClass(): string;

    protected function getSubdomainCookieHost(): string
    {
        $host  = $this->getCookieBaseDomain();
        $parts = explode('.', $host);
        array_shift($parts);

        return implode('.', $parts);
    }

    protected function getCookieBaseDomain()
    {
        return $_SERVER['HTTP_HOST'] ?? parse_url(URLManager::getShopURL(), PHP_URL_HOST);
    }

    /**
     * @return bool
     */
    protected function isTagActive(): bool
    {
        return XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            $tagWidget = $this->getTagWidget();

            return $tagWidget && $tagWidget->isActive();
        }, [
            __METHOD__,
            $this->getFinalTagWidgetParams(),
        ]);
    }
}
