<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Base\Surcharge;
use XLite\Model\Currency;
use XLite\Model\Order\Status;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    protected function doActionUpdate()
    {
        /** @var \CDev\GoogleAnalytics\Model\Order $order */
        $order = $this->getOrder();
        if ($order && $order->shouldRegisterChange()) {
            $old = $this->collectData($order);

            $needRegisterChanges = $this->needRegisterChanges();

            parent::doActionUpdate();

            $new = $this->collectData($order);

            if ($needRegisterChanges) {
                $this->registerEvent(
                    $this->getGAChanges($old, $new)
                );
            }
        } else {
            parent::doActionUpdate();
        }
    }

    protected function collectData(\XLite\Model\Order $order): array
    {
        $tax      = $order->getSurchargeSumByType(Surcharge::TYPE_TAX);
        $shipping = $order->getSurchargeSumByType(Surcharge::TYPE_SHIPPING);

        return [
            'revenue'  => $order->getTotal(),
            'tax'      => $tax,
            'shipping' => $shipping,
            'items'    => $this->getItemsFingerprint($order->getItems()),
        ];
    }

    protected function getItemsFingerprint($items): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[$item->getItemId()] = [
                'item'   => $item,
                'amount' => $item->getAmount(),
            ];
        }

        return $result;
    }

    protected function needRegisterChanges(): bool
    {
        $order = $this->getOrder();

        if (!$order) {
            return false;
        }

        return $order->getPaymentStatusCode() === Status\Payment::STATUS_PAID
            || (
                GA::getResource()->isPurchaseImmediatelyOnSuccess()
                && in_array($order->getPaymentStatusCode(), [
                    Status\Payment::STATUS_AUTHORIZED,
                    Status\Payment::STATUS_QUEUED,
                ], true)
            );
    }

    protected function registerEvent($changes): void
    {
        if (!array_filter($changes)) {
            return;
        }

        $changesToRegister = [
            'revenue'  => $changes['revenue'],
            'tax'      => $changes['tax'],
            'shipping' => $changes['shipping'],
        ];

        $refundItems   = [];
        $purchaseItems = [];
        foreach ($changes['items'] as $itemData) {
            if ($itemData['change'] > 0) {
                $purchaseItems[] = $itemData;
            } elseif ($itemData['change'] < 0) {
                $refundItems[] = $itemData;
            }
        }

        if ($purchaseItems) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\PurchaseAdmin(
                    $this->getOrder(),
                    $purchaseItems
                )
            );
        }

        if ($refundItems) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\Refund(
                    $this->getOrder(),
                    $refundItems
                )
            );
        }

        $haveChangesToRegister = (bool) array_filter(array_values($changesToRegister));
        if ($haveChangesToRegister) {
            GA::getBackendExecutor()->execute(
                new Action\Backend\TotalChange(
                    $this->getOrder(),
                    $changesToRegister
                )
            );
        }
    }

    protected function getGAChanges(array $old, array $new): array
    {
        $currency = $this->getCurrency();

        if (!$currency) {
            return [];
        }

        $changes = [];

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key];

            if ($key === 'items') {
                $changes[$key] = $this->getItemsChange($old[$key], $newValue);
            } else {
                $changes[$key] = $currency->roundValue($newValue - $oldValue);
            }
        }

        return array_combine(
            array_keys($new),
            array_values($changes)
        );
    }

    protected function getCurrency(): ?Currency
    {
        $order = $this->getOrder();

        return $order ? $order->getCurrency() : null;
    }

    protected function getItemsChange($oldItems, $newItems): array
    {
        $currency = $this->getCurrency();

        if (!$currency) {
            return [];
        }

        $allItems = [];
        foreach ($oldItems as $id => $item) {
            $allItems[$id] = $item['item'];
        }
        foreach ($newItems as $id => $item) {
            $allItems[$id] = $item['item'];
        }

        $changes = [];
        foreach ($allItems as $itemId => $item) {
            $oldAmount        = $oldItems[$itemId]['amount'] ?? 0;
            $newAmount        = $newItems[$itemId]['amount'] ?? 0;
            $changes[$itemId] = [
                'item'   => $item,
                'change' => $currency->roundValue($newAmount - $oldAmount),
            ];
        }

        return $changes;
    }
}
