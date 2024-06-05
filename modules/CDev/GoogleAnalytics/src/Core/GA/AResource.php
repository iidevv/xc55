<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use XLite;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;
use XLite\Core\ConfigCell;
use CDev\GoogleAnalytics\Core\GA\Interfaces;

abstract class AResource implements Interfaces\IResource
{
    use ExecuteCachedTrait;

    /**
     * @var ConfigCell
     */
    protected $config;

    /**
     * @var string
     */
    protected $libraryClass;

    /**
     * @var Interfaces\IDataMapperRegistry
     */
    protected $dataMapperRegistry;

    public function __construct(string $libraryClass)
    {
        $this->config = Config::getInstance()->CDev->GoogleAnalytics;

        $this->libraryClass = $libraryClass;
    }

    public function __call($method, array $args)
    {
        return null;
    }

    public function isConfigured(): bool
    {
        return $this->getMeasurementId();
    }

    public function isECommerceEnabled(): bool
    {
        return (bool) $this->config->ecommerce_enabled;
    }

    public function isPurchaseImmediatelyOnSuccess(): bool
    {
        return !$this->config->purchase_only_on_paid;
    }

    public function getTrackingType(): int
    {
        return (int) $this->config->ga_tracking_type;
    }

    public function isDebugMode(): bool
    {
        return (bool) $this->config->debug_mode;
    }

    public function getCurrencyCode(): string
    {
        return XLite::getInstance()->getCurrency()
            ? XLite::getInstance()->getCurrency()->getCode()
            : 'USD';
    }

    public function getTagWidgetParams(): array
    {
        return [];
    }

    public function getLibraryClass(): string
    {
        return $this->libraryClass;
    }

    public function getDataMapperRegistry(): Interfaces\IDataMapperRegistry
    {
        if ($this->dataMapperRegistry === null) {
            $this->defineDataMapperRegistry();
        }

        return $this->dataMapperRegistry;
    }

    protected function defineDataMapperRegistry(): void
    {
        $this->dataMapperRegistry = new DataMapperRegistry(static::class, $this->libraryClass);
    }
}
