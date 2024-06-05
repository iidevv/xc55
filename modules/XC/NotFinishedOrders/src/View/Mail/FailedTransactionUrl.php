<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\Mail;

use XCart\Extender\Mapping\ListChild;
use XLite\Model\Payment\Transaction;

/**
 * FailedTransactionUrl
 *
 * @ListChild(list="failed_transaction.after", interface="mail", zone="common")
 */
class FailedTransactionUrl extends \XLite\View\AView
{
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getTransaction() instanceof Transaction;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/NotFinishedOrders/failed_transaction/body.twig';
    }

    /**
     * @return Transaction|null
     */
    protected function getTransaction()
    {
        return $this->transaction ?? null;
    }

    /**
     * @return boolean
     */
    protected function isNfo()
    {
        $order = $this->getTransaction()->getOrder();

        return $order
            && is_null($order->getShippingStatus());
    }

    /**
     * @return string
     */
    protected function getNfoUrl()
    {
        $dateRange = \XLite\View\FormField\Input\Text\DateRange::convertToStringStatic([
            strtotime('today', $this->getTransaction()->getDate()),
            strtotime('tomorrow', $this->getTransaction()->getDate()) - 1,
        ]);

        $statuses          = \XLite\Model\Payment\Transaction::getStatuses();
        $statuses          = array_filter(
            array_keys($statuses),
            static function ($v) {
                return $v !== \XLite\Model\Payment\Transaction::STATUS_SUCCESS;
            }
        );
        $httpReadyStatuses = [];
        $i                 = 0;
        foreach ($statuses as $status) {
            $httpReadyStatuses['status[' . $i++ . ']'] = $status; // 'status[0]' => 'F','status[1]' => 'I',
        }

        return \XLite\Core\Converter::buildFullURL(
            'payment_transactions',
            '',
            array_merge([
                'date'         => $dateRange,//all today transactions
                'customerName' => $this->getTransaction()->getOrder()->getProfile()->getLogin(),
            ], $httpReadyStatuses),
            \XLite::getAdminScript()
        );
    }
}
