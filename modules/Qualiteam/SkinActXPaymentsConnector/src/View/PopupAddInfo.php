<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View;

use Qualiteam\SkinActXPaymentsConnector\Core\XPaymentsClient;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Payment\Transaction;
use XLite\View\AView;

/**
 * Popup payment additional info
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupAddInfo extends AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('popup_add_info'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/order/add_info';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return formatted time
     *
     * @param string $time
     *
     * @return string
     */
    public function getTime($time)
    {
        return Converter::getInstance()->formatTime(intval($time));
    }

    /**
     * Get X-Payments connector transactions
     *
     * @return boolean
     */
    public function getXpcTransactionsAddInfo()
    {
        $transaction = Database::getRepo(Transaction::class)->find(
            Request::getInstance()->transaction_id
        );

        $result = false;

        if (
            $transaction
            && $transaction->isXpc(true)
            && $transaction->getDataCell('xpc_txnid')
        ) {

            $client = XPaymentsClient::getInstance();

            $info = $client->requestPaymentAdditionalInfo($transaction->getDataCell('xpc_txnid')->getValue());

            if ($info->isSuccess()) {

                $response = $info->getResponse();
                if (
                    !empty($response['transactions'])
                    && is_array($response['transactions'])
                ) {
                    $result = $response['transactions'];
                }
            }
        }

        return $result;
    }
}
