<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View;

use XCart\Extender\Mapping\ListChild;



use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\Session;

/**
 * Common resources loader
 *
 * @ListChild (list="layout.main",zone="customer",weight="1")
 */
class WebViewMessagingBridge extends \XLite\View\AView
{
    /**
     * @var array
     */
    protected $authEvent = null;

    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Return widget default template
     *
     * @param null $template
     *
     * @return string
     */
    protected function doDisplay($template = null)
    {
        $message = $this->wrapMessage($this->getStatusMessage());

        echo <<<SCRIPT
<script>
{
  let message = {$message}
  
  function postMessageToApp(message) {
    if (isIOS()) {
      window.webkit.messageHandlers.status.postMessage(message)
    } else if (isAndroid()) {
      window.statusMessageHandler.postMessage(JSON.stringify(message))
    } else {
      console.log("Unable to post message to app: ", message)
    }
  }
  
  function isIOS() {
    return typeof window.webkit !== 'undefined'
        && typeof window.webkit.messageHandlers !== 'undefined'
        && typeof window.webkit.messageHandlers.status !== 'undefined'
  }
  
  function isAndroid() {
    return typeof window.statusMessageHandler !== 'undefined'
        && typeof window.statusMessageHandler.postMessage === 'function'
  }
  
  postMessageToApp(message)
}
</script>
SCRIPT;
    }

    protected function wrapMessage($message)
    {
        return json_encode($message);
    }

    protected function getStatusMessage()
    {
        return [
            'last_order_number' => $this->getLastOrderNumber(),
            'status' => $this->getOperationStatus(),
            'data' => $this->getOperationData(),
            'messages' => $this->getOperationMessages()
        ];
    }

    /**
     * @return array
     */
    protected function getOperationMessages()
    {
        return \XLite\Core\TopMessage::getInstance()->getPreviousMessages();
    }

    protected function getOperationData()
    {
        if ($this->isOrderSuccessful()) {
            return [
                'last_order_number' => $this->getLastOrderNumber()
            ];
        }

        if ($this->isAuthSuccessful() || $this->isAuthFailure()) {
            return $this->getAuthEventData();
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getOperationStatus()
    {
        if ($this->isOrderSuccessful()) {
            return 'success';
        }

        if ($this->isAuthSuccessful()) {
            return 'auth_success';
        }

        if ($this->isAuthFailure()) {
            return 'auth_failure';
        }

        if ($this->isCheckoutErrorHappened()) {
            return 'errors';
        }

        return '';
    }

    protected function isCheckoutErrorHappened()
    {
        return !empty($this->getOperationMessages())
            && \XLite::getController()->getTarget() === 'checkout';
    }

    protected function isAuthSuccessful()
    {
        $event = $this->getAuthEventData();
        return $event && $event['success'] === true;
    }

    protected function isAuthFailure()
    {
        $event = $this->getAuthEventData();
        return $event && $event['success'] === false;
    }

    /**
     * @return bool
     */
    protected function isOrderSuccessful()
    {
        return $this->getLastOrderNumber()
            && \XLite::getController() instanceof \XLite\Controller\Customer\CheckoutSuccess;
    }

    protected function getAuthEventData()
    {
        if (!$this->authEvent) {
            // consuming the event
            $this->authEvent = Session::getInstance()->oauth2event;
            Session::getInstance()->oauth2event = null;
        }

        return $this->authEvent;
    }

    /**
     * @return string
     */
    protected function getLastOrderNumber()
    {
        $lastOrderId = \XLite\Core\Session::getInstance()->last_order_id;

        /** @var \XLite\Model\Order $order */
        $order = Database::getRepo(\XLite\Model\Order::class)->find($lastOrderId);

        return $lastOrderId && $order && $order->getOrderNumber()
            ? $order->getPrintableOrderNumber()
            : '';
    }
}
