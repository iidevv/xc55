<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Controller\Customer;

use Qualiteam\SkinActAftership\Helpers\TrackingsHelper;
use Qualiteam\SkinActAftership\Model\AftershipCouriers;
use Qualiteam\SkinActAftership\Model\ShipstationCodeMapping;
use XCart\Container;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Database;
use XLite\Core\OrderHistory;
use XLite\Model\Order;
use XLite\Model\OrderTrackingNumber;

/**
 * Class api
 * @Extender\Mixin
 * @Extender\Depend ("ShipStation\Api")
 */
class Api extends \ShipStation\Api\Controller\Customer\Api
{
    /** @var $shipStationListOrders */
    protected $shipStationListOrders;

    /**
     * Function to update the order status
     */
    protected function actionStatusUpdate()
    {
        if ($_GET['order_number']) {
            $comments    = $_GET['comment'];
            $orderNumber = (int) $_GET['order_number'];

            $order = Database::getRepo(Order::class)
                ->findOneByOrderNumber($orderNumber);

            if ($order && $comments) {
                $carrierCode = $this->getCarrierCodeByOrderNumber($orderNumber);

                $trackingNumberDb = Database::getRepo(OrderTrackingNumber::class)
                    ->findOneBy([
                        'order' => $order,
                        'value' => $comments,
                    ]);

                if (!$trackingNumberDb) {
                    $trackingNumberDb = new OrderTrackingNumber();
                    $trackingNumberDb->setOrder($order);
                    $trackingNumberDb->setValue($comments);
                }

                if (!empty($carrierCode)) {

                    if ($this->hasCarrierCodeFix($carrierCode)) {
                        $carrierCode = $this->getCarrierCodeFix($carrierCode)->getAftershipSlug();
                    }

                    $afterShipCourier = Database::getRepo(AftershipCouriers::class)
                        ->findOneBySlug($carrierCode);

                    $afterShipCourierName = '';

                    if ($afterShipCourier) {
                        $afterShipCourierName = $afterShipCourier->getName();
                    }

                    if (!$afterShipCourierName) {
                        $afterShipCourierName = $this->getCarrierCompanyNameBySlug($carrierCode);
                    }

                    if (
                        empty($afterShipCourierName)
                        && $carrierCode !== 'customco-api'
                    ) {
                        OrderHistory::getInstance()->getAftershipCouriersError($order->getOrderId(), $carrierCode);
                        $trackingNumberDb->setShipstationSlugError(true);
                    }

                    $trackingNumberDb->setAftershipCourierName($afterShipCourierName);

                    if (!empty($afterShipCourierName)) {
                        $aftershipResult = TrackingsHelper::addAftershipTracking($comments, $carrierCode);

                        if (TrackingsHelper::hasAftershipResult($aftershipResult)) {
                            $trackingNumberDb->setAftershipSync(true);
                        }
                    }
                }

                Database::getEM()->persist($trackingNumberDb);
                Database::getEM()->flush();
            }
        }

        parent::actionStatusUpdate();
    }

    /**
     * Get carrier code by order id
     *
     * @param int $orderNumber
     *
     * @return string
     */
    protected function getCarrierCodeByOrderNumber(int $orderNumber): string
    {
        if (!empty($orderNumber)) {
            $container                   = Container::getContainer()->get('shipstation.api');
            $this->shipStationListOrders = $container->getListOrders([
                'orderNumber' => $orderNumber,
            ]);

            if ($this->hasShipStationOrderInternalNotes()) {
                return $this->getCarrierCodeByInternalNotes();
            }

            if ($this->hasShipStationOrderCarrierCode()) {
                return $this->getShipStationOrderCarrierCode();
            }

        }

        return '';
    }

    /**
     * If ship station order has carrier code
     *
     * @return bool
     */
    protected function hasShipStationOrderCarrierCode(): bool
    {
        return $this->hasShipStationOrder()
            && $this->shipStationListOrders['orders'][0]['carrierCode'];
    }

    /**
     * Has ship station order
     *
     * @return bool
     */
    protected function hasShipStationOrder(): bool
    {
        return $this->hasShipStationOrders()
            && $this->shipStationListOrders['orders'][0];
    }

    /**
     * Has ship station orders
     *
     * @return bool
     */
    protected function hasShipStationOrders(): bool
    {
        return $this->shipStationListOrders
            && $this->shipStationListOrders['orders'];
    }

    /**
     * Get ship station order carrier code
     *
     * @return string
     */
    protected function getShipStationOrderCarrierCode(): string
    {
        return $this->shipStationListOrders['orders'][0]['carrierCode'];
    }

    /**
     * Has ship station internal notes
     *
     * @return bool
     */
    protected function hasShipStationOrderInternalNotes(): bool
    {
        return $this->hasShipStationOrder()
            && $this->shipStationListOrders['orders'][0]['internalNotes'];
    }

    /**
     * Get carrier code by internal notes
     *
     * @return string
     */
    protected function getCarrierCodeByInternalNotes(): string
    {
        return $this->shipStationListOrders['orders'][0]['internalNotes'];
    }

    /**
     * Has carrier code fix
     *
     * @param string $slug
     *
     * @return bool
     */
    protected function hasCarrierCodeFix(string $slug): bool
    {
        return (bool) $this->findCarrierFixAbbr($slug);
    }

    /**
     * Carriers' abbreviation mappings (used when tracking is received from ShipStation)
     */
    protected function findCarrierFixAbbr(string $abbr)
    {
        return $this->executeCachedRuntime(function () use ($abbr) {
            return Database::getRepo(ShipstationCodeMapping::class)
                ->findOneBy(['shipstation_slug' => $abbr]);
        }, [
            __CLASS__,
            __METHOD__,
            $abbr,
        ]);
    }

    /**
     * Get carrier code fix
     *
     * @param string $slug
     *
     * @return ShipstationCodeMapping|null
     */
    protected function getCarrierCodeFix(string $slug): ?ShipstationCodeMapping
    {
        return $this->findCarrierFixAbbr($slug);
    }

    /**
     * Get carrier company name by slug
     *
     * @param string $slug
     *
     * @return ?string
     */
    protected function getCarrierCompanyNameBySlug(string $slug): ?string
    {
        $list = [
            'rl-carriers'        => 'R & L Freight Company [rl-carriers]',
            'roadrunner-freight' => 'Road Runner Freight [roadrunner-freight]',
            'xpo-logistics'      => 'XPO Logistics [xpo-logistics]',
            'dylt'               => 'Day-Light Transportation Freight [dylt]',
            'pilot-freight'      => 'Pilot Freight Services [pilot-freight]',
            'customco-api'           => '', //'Customco.com [customco]',
        ];

        return !empty($slug) && isset($list[$slug]) ? $list[$slug] : '';
    }

    /**
     * Prepare result internal notes
     *
     * @return string
     */
    protected function prepareResultInternalNotes(): string
    {
        return strtolower(
            trim(
                $this->shipStationListOrders['orders'][0]['internalNotes']
            )
        );
    }
}