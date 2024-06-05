<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Core;

use XLite;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Config;
use XLite\Core\Exception\MethodNotFound;
use XLite\Model\Cart;
use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA\Interfaces\{ILibrary, IResource};
use CDev\GoogleAnalytics\Core\GA\Library\Dummy;
use CDev\GoogleAnalytics\Core\GA\ResourceSelector;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\ActionsStorage;

class GA extends \XLite\Base
{
    use ExecuteCachedTrait;

    public const CODE_VERSION_U = 'U';
    public const CODE_VERSION_4 = '4';

    public const LIBRARY_ANALYTICS = 'analytics';
    public const LIBRARY_GTAG      = 'gtag';

    /**
     * @var IResource|null
     */
    private $resource;

    /**
     * @var ILibrary|null
     */
    private $library;

    public function __construct()
    {
        parent::__construct();

        $this->defineResource();
        $this->defineLibrary();
        $this->initActions();
    }

    protected function defineResource(): void
    {
        $codeVersion = Config::getInstance()->CDev->GoogleAnalytics->ga_code_version;

        $this->resource = (new ResourceSelector($codeVersion))->getResource();
    }

    public static function getResource(): IResource
    {
        return static::getInstance()->getCorrectResource();
    }

    protected function getCorrectResource(): IResource
    {
        if (!$this->resource instanceof IResource) {
            $this->resource = new GA\Resource\Dummy('');
        }

        return $this->resource;
    }

    protected function defineLibrary(): void
    {
        if (
            $this->resource instanceof IResource
            && ($libraryClass = $this->resource->getLibraryClass())
            && (is_a($libraryClass, ILibrary::class, true))
        ) {
            $this->library = new $libraryClass($this->resource);
        }
    }

    public function initActions(): void
    {
        if ($cart = $this->getCurrentCartIfAvailable()) {
            $this->addCartBasedActions($cart);
        }

        if ($order = $this->getCurrentOrderIfAvailable()) {
            $this->addOrderBasedActions($order);
        }
    }

    protected function getCurrentCartIfAvailable(): ?Cart
    {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        return method_exists(XLite::getController(), 'getCart')
            ? XLite::getController()->getCart(false)
            : null;
    }

    protected function addCartBasedActions($cart): void
    {
        ActionsStorage::getInstance()->addAction(
            new Action\CheckoutInit($cart)
        );
        ActionsStorage::getInstance()->addAction(
            new Action\ViewCart($cart)
        );
    }

    protected function getCurrentOrderIfAvailable(): ?Order
    {
        $controller = \XLite::getController();

        try {
            return method_exists($controller, 'getOrder') && is_callable([$controller, 'getOrder'])
                ? $controller->getOrder()
                : null;
        } catch (MethodNotFound $e) {
            return null;
        }
    }

    protected function addOrderBasedActions($order): void
    {
        ActionsStorage::getInstance()->addAction(
            new Action\Purchase($order)
        );

        ActionsStorage::getInstance()->addAction(
            new Action\CheckoutComplete($order)
        );
    }

    public static function getProductDataMapper(): GA\Interfaces\DataMappers\IProduct
    {
        return static::getResource()->getDataMapperRegistry()->getProductMapper();
    }

    public static function getOrderItemDataMapper(): GA\Interfaces\DataMappers\IOrderItem
    {
        return static::getResource()->getDataMapperRegistry()->getOrderItemMapper();
    }

    public static function getOrderDataMapper(): GA\Interfaces\DataMappers\IOrder
    {
        return static::getResource()->getDataMapperRegistry()->getOrderMapper();
    }

    public static function getBackendExecutor(): GA\Interfaces\IBackendActionExecutor
    {
        return static::getResource()->getBackendExecutorClass()::getInstance();
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get($name)
    {
        switch ($name) {
            case 'resource':
                return static::getResource();
            case 'library':
                return static::getLibrary();
            default:
                return $this->getUndefinedProperty((string) $name);
        }
    }

    public static function getLibrary(): ILibrary
    {
        return static::getInstance()->getCorrectLibrary();
    }

    protected function getCorrectLibrary(): ILibrary
    {
        if (!$this->library instanceof ILibrary) {
            $this->library = new Dummy(static::getResource());
        }

        return $this->library;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getUndefinedProperty(string $name)
    {
        return null;
    }

    public function __call($method, array $args = [])
    {
        if ($this->resource && method_exists($this->resource, $method)) {
            return call_user_func_array([$this->resource, $method], $args);
        }

        parent::__call($method, $args);

        return null;
    }
}
