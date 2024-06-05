<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Admin;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XLite\Controller\Admin\AAdmin;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;

/**
 * Subscriptions list controller
 */
class XPaymentsSubscription extends AAdmin
{
    /**
     * Is search visible
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return true;
    }

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $subscriptionId = isset(Request::getInstance()->subscription_id)
            ? Request::getInstance()->subscription_id
            : 0;

        $subscription = Database::getRepo(Subscription::class)
            ->find($subscriptionId);

        return ($subscription)
            ? static::t('Subscription #{{id}}', ['id' => $subscriptionId])
            : static::t('Subscriptions');
    }

    /**
     * Define the session cell name for the subscriptions list
     *
     * @return string
     */
    protected function getSessionCellName()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model\Subscription::getSessionCellName();
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $searchParams = Session::getInstance()->{$this->getSessionCellName()};

        return is_array($searchParams) ? $searchParams : [];
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $data = Request::getInstance()->getData();

        foreach ($data['data'] as $id => $row) {
            $subscription = Database::getRepo(Subscription::class)
                ->find($id);

            if (!$subscription) {
                continue;
            }

            if (
                isset($row['card'])
                && (
                    !$subscription->getXpcData()
                    || $row['card'] != $subscription->getXpcData()->getId()
                )
            ) {
                $card = Database::getRepo(XpcTransactionData::class)
                    ->find($row['card']);
                if ($card) {
                    $subscription->setXpcData($card);
                }
            }

            if (
                isset($row['shipping_address'])
                && (
                    !$subscription->getShippingAddress()
                    || $row['shipping_address'] != $subscription->getShippingAddress()->getAddressId())
            ) {
                $shippingAddress = Database::getRepo('XLite\Model\Address')
                    ->find($row['shipping_address']);
                if ($shippingAddress) {
                    $oldAddress = $subscription->getShippingAddress();
                    $newAddress = $shippingAddress->cloneEntity();
                    $newAddress->setProfile(null);
                    $newAddress->create();
                    $subscription->setShippingAddress($newAddress);
                    if ($oldAddress->getProfile() === null) {
                        $oldAddress->delete();
                    }
                }
            }
        }

        $list = new \Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model\Subscription();
        $list->processQuick();
    }
}
