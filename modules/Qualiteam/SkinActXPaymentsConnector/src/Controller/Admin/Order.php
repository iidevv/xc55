<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Admin;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment\BackendTransaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Payment\Transaction;

/**
 * Order page controller
 *
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Get X-Payments connector transactions 
     * 
     * @return boolean
     */
    public function getXpcTransactions()
    {
        $cnd = new CommonCell();
        $class = BackendTransaction::class;

        if (
            Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
            && $this->getOrder()->getParent()
        ) {
            $order = $this->getOrder()->getParent();
        } else {
            $order = $this->getOrder();
        }

        $cnd->{$class::SEARCH_ORDER_ID} = $order->getOrderId();

        $count = Database::getRepo(XpcTransactionData::class)
            ->search($cnd, true);

        return $count > 0;
    }

    /**
     * Do Recharge action
     *
     * @return void
     */
    public function doActionRecharge()
    {
        if (
            Request::getInstance()->trn_id
            && Request::getInstance()->amount
            && $this->getOrder()
        ) {
    
            $parentCardTransaction = Database::getRepo(Transaction::class)->find(Request::getInstance()->trn_id);
            $amount = number_format(Request::getInstance()->amount, 2, '.', '');

            $parentCardTransaction->getPaymentMethod()->getProcessor()->doRecharge(
                $this->getOrder(),
                $parentCardTransaction,
                $amount,
                false
            );
        }

        $this->redirectBackToOrder();
    }

    /**
     * Order number wrapper 
     *
     * @return integer
     */
    public function getOrderNumber() 
    {
        return $this->getOrder()->getOrderNumber();
    }

    /**
     * Redirect admin back to the order page (controller's redirecter wrapper) 
     *
     * @return void
     */
    public function redirectBackToOrder() 
    {
        $this->setHardRedirect();

        $this->setReturnURL(
            $this->buildURL(
                'order',
                '',
                array(
                    'order_number'  => $this->getOrderNumber(),
                )
            )
        );

        $this->doRedirect();

        exit;
    }

    /**
     * Return true if order can be edited
     *
     * @return boolean
     */
    public function isOrderEditable()
    {
        $isEditable = parent::isOrderEditable();
        $order = $this->getOrder();
        foreach ($order->getItems() as $item) {
            if ($item->isXpcFakeItem()) {
                $isEditable = false;
                break;
            }
        }

        return $isEditable;
    }
}
