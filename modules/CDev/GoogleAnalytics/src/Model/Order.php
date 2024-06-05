<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace CDev\GoogleAnalytics\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Request;
use XLite\Logic\Order\Modifier\Shipping;
use XLite\Model\Base\Surcharge;
use XLite\Model\CategoryTranslation;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Shipping\Method;
use XLite\Model\Shipping\MethodTranslation;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;

/**
 * Class Order
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order
{
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $result = parent::addItem($newItem);

        if (
            $result
            && !$this->addItemError
            && $this->shouldRegisterChange()
            && $newItem->getObject()
            && $newItem->getObject()->getCategory()
        ) {
            $category    = $newItem->getObject()->getCategory(
                Request::getInstance()->category_id
            );
            /** @var CategoryTranslation $translation */
            $translation = $category->getSoftTranslation(
                Config::getInstance()->General->default_language
            );

            if ($translation) {
                /** @var OrderItem $newItem */
                $newItem->setCategoryAdded(
                    $translation->getName()
                );
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function shouldRegisterChange()
    {
        return GA::getResource()->isConfigured()
            && GA::getResource()->isECommerceEnabled()
            && !$this->isTemporary();
    }

    /**
     * Get order fingerprint for event subsystem
     *
     * @param array $exclude Exclude kes OPTIONAL
     *
     * @return array
     */
    public function getEventFingerprint(array $exclude = [])
    {
        $result = parent::getEventFingerprint($exclude);

        if ($this->shouldRegisterChange()) {
            // Just for implementation without decoration of all excludeFingerprint implementations
            if (!isset($result['shippingMethodId']) && isset($result['shippingMethodName'])) {
                unset($result['shippingMethodName']);
            }

            if (!isset($result['paymentMethodId']) && isset($result['paymentMethodName'])) {
                unset($result['paymentMethodName']);
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        $list = parent::defineFingerprintKeys();
        if ($this->shouldRegisterChange()) {
            return array_merge(
                $list,
                [
                    'shippingMethodName',
                    'paymentMethodName',
                ]
            );
        }

        return $list;
    }

    /**
     * Get fingerprint by 'shippingMethodName' key
     *
     * @return string
     */
    protected function getFingerprintByShippingMethodName()
    {
        /** @var Method|MethodTranslation $method */
        return ($method = $this->getShippingModifierMethod())
            ? $method->getName()
            : '';
    }

    protected function getShippingModifierMethod(): ?Method
    {
        $shippingModifier = $this->getModifier(Surcharge::TYPE_SHIPPING, 'SHIPPING');

        /** @var Shipping $shippingModifier */
        if (
            $shippingModifier
            && ($rate = $shippingModifier->getSelectedRate())
            && ($method = $rate->getMethod())
        ) {
            return $method;
        }

        return null;
    }

    /**
     * Get fingerprint by 'paymentMethodName' key
     *
     * @return string
     */
    protected function getFingerprintByPaymentMethodName()
    {
        /** @var \XLite\Model\Payment\MethodTranslation $method */
        return ($method = $this->getPaymentMethod())
            ? $method->getTitle()
            : '';
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGAPurchase()
    {
        // If isPurchaseImmediatelyOnSuccess enabled 'purchase' was already registered, so skip STATUS_QUEUED
        if (
            GA::getResource()->isPurchaseImmediatelyOnSuccess()
            && in_array($this->getOldPaymentStatusCode(), [
                Payment::STATUS_QUEUED,
                Payment::STATUS_AUTHORIZED,
            ], true)
        ) {
            return;
        }

        if ($this->shouldRegisterChange()) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\FullPurchaseAdmin($this)
            );
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGARefund()
    {
        if ($this->shouldRegisterChange()) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\Refund($this)
            );
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processRegisterGARefundFromQueued()
    {
        // If we did not register purchase on checkout then we should not register refund
        if (!GA::getResource()->isPurchaseImmediatelyOnSuccess()) {
            return;
        }

        if ($this->shouldRegisterChange()) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\Refund($this)
            );
        }
    }

    /**
     * @return string
     */
    public function getGaClientId()
    {
        /** @var Profile $profile */
        $profile = $this->getOrigProfile();

        if (!$profile) {
            // Anonymous
            $profile = $this->getProfile();
        }

        // Backend commands will fallback to the session, see \CDev\GoogleAnalytics\Logic\Action\Backend\PurchaseAdmin::getClientId
        return $profile ? $profile->getGaClientId() : "";
    }
}
