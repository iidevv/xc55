<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core\GA;

use CDev\GoogleAnalytics\Core\GA\DataMappers;
use CDev\GoogleAnalytics\Core\GA\Interfaces;
use CDev\GoogleAnalytics\Core\GA\Library;
use CDev\GoogleAnalytics\Core\GA\Resource;

class DataMapperRegistry implements Interfaces\IDataMapperRegistry
{
    public const COMBINE_UAGA    = 'UAGA';
    public const COMBINE_GA4GTAG = 'GA4Gtag';

    /**
     * @var string
     */
    protected $resourceClass;

    /**
     * @var string
     */
    protected $libraryClass;

    /**
     * @var string
     */
    protected $mapperClassName;

    /**
     * @var Interfaces\DataMappers\IProduct
     */
    protected $productMapper;

    /**
     * @var Interfaces\DataMappers\IOrderItem
     */
    protected $orderItemMapper;

    /**
     * @var Interfaces\DataMappers\IOrder
     */
    protected $orderMapper;

    public function __construct(string $resourceClass, string $libraryClass)
    {
        $this->resourceClass   = $resourceClass;
        $this->libraryClass    = $libraryClass;
        $this->mapperClassName = $this->classNameSelector();

        $this->defineMappers();
    }

    protected function classNameSelector(): ?string
    {
        if (
            is_a($this->resourceClass, Resource\Universal::class, true)
            && is_a($this->libraryClass, Library\Analytics::class, true)
        ) {
            return static::COMBINE_UAGA;
        }

        if (
            is_a($this->resourceClass, Resource\GA4::class, true)
            && is_a($this->libraryClass, Library\GTag::class, true)
        ) {
            return static::COMBINE_GA4GTAG;
        }

        return null;
    }

    protected function defineMappers(): void
    {
        $this->defineProductMapper();
        $this->defineOrderItemMapper();
        $this->defineOrderMapper();
    }

    protected function defineProductMapper(): void
    {
        $mapper = new DataMappers\Product();

        $className = static::productMapperClasses()[$this->mapperClassName] ?? null;

        if (is_a($className, DataMappers\AMapper::class, true)) {
            $mapper = new $className($mapper);
        }

        $this->productMapper = $mapper;
    }

    protected static function productMapperClasses(): array
    {
        return [
            static::COMBINE_UAGA    => DataMappers\Product\UAGA::class,
            static::COMBINE_GA4GTAG => DataMappers\Product\GA4Gtag::class,
        ];
    }

    protected function defineOrderItemMapper(): void
    {
        $mapper = new DataMappers\OrderItem();

        $className = static::orderItemMapperClasses()[$this->mapperClassName] ?? null;

        if (is_a($className, DataMappers\AMapper::class, true)) {
            $mapper = new $className($mapper);
        }

        $this->orderItemMapper = $mapper;
    }

    protected static function orderItemMapperClasses(): array
    {
        return [
            static::COMBINE_UAGA    => DataMappers\OrderItem\UAGA::class,
            static::COMBINE_GA4GTAG => DataMappers\OrderItem\GA4Gtag::class,
        ];
    }

    protected function defineOrderMapper(): void
    {
        $mapper = new DataMappers\Order();

        $className = static::orderMapperClasses()[$this->mapperClassName] ?? null;

        if (is_a($className, DataMappers\AMapper::class, true)) {
            $mapper = new $className($mapper);
        }

        $this->orderMapper = $mapper;
    }

    protected static function orderMapperClasses(): array
    {
        return [
            static::COMBINE_UAGA    => DataMappers\Order\UAGA::class,
            static::COMBINE_GA4GTAG => DataMappers\Order\GA4Gtag::class,
        ];
    }

    public function getProductMapper(): Interfaces\DataMappers\IProduct
    {
        return $this->productMapper;
    }

    public function getOrderItemMapper(): Interfaces\DataMappers\IOrderItem
    {
        return $this->orderItemMapper;
    }

    public function getOrderMapper(): Interfaces\DataMappers\IOrder
    {
        return $this->orderMapper;
    }
}
