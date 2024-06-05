<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Controller\Customer;

use Qualiteam\SkinActYotpoReviews\Core\Dispatcher\CreateOrderDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    protected ?MessageBusInterface $bus;

    protected function doNoAction()
    {
        parent::doNoAction();

        $orderNumber = $this->getOrderNumber();

        if ($orderNumber) {
            $this->setMessageBus(
                $this->getBusContainer()
            );

            if ($this->getMessageBus()) {
                $dispatcher = $this->getOrderDispatcher();

                $message = $dispatcher->getMessage();
                $this->getMessageBus()->dispatch($message);
            }
        }
    }

    protected function getOrderDispatcher()
    {
        return new CreateOrderDispatcher($this->getOrder());
    }

    protected function getOrderNumber()
    {
        return Request::getInstance()->order_number;
    }

    protected function setMessageBus(?MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    protected function getBusContainer()
    {
        return Container::getContainer()
            ? Container::getContainer()?->get('messenger.default_bus')
            : null;
    }

    protected function getMessageBus()
    {
        return $this->bus;
    }
}