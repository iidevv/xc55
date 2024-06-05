<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Customer;

use XLite\Controller\Customer\ACustomer;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Core\Converter;

class QbwcSyncOrders extends ACustomer
{
    /**
     * Handles the request
     */
    public function handleRequest()
    {
        if (!QuickbooksConnector::isSyncEnabled()) {
            die('Access denied');
        }
        
        QuickbooksConnector::initialize();
        
        parent::handleRequest();
    }
    
    /**
     * Sync customers
     */
    public function doNoAction()
    {
        QuickbooksConnector::loadSDK();
        
        $object = new QuickbooksConnector();
        
        $map = array(
            QUICKBOOKS_ADD_SALESORDER => array(
                [$object, 'qbcSalesorderAddRequest'],
                [$object, 'qbcSalesorderAddResponse'],
            ),
        );

        $errmap = array(
            '*'  => [$object, 'qbcErrorCatchAll'],
        );
        
        if (empty(Session::getInstance()->qbwc_first_access_time)) {
            Session::getInstance()->qbwc_first_access_time = Converter::time();
        }
        
        if (Session::getInstance()->qbwc_first_access_time == Converter::time()) {
        
            QuickbooksConnector::qbcFilterQueue($map);

            $orders = Database::getRepo('XLite\Model\Order')
                ->getOrderIdsForSync();

            if (!empty($orders)) {
                foreach ($orders as $order_id) {
                    // Adding order to queue
                    QuickbooksConnector::qbcQueueRequest(
                        QUICKBOOKS_ADD_SALESORDER,
                        $order_id
                    );
                }
            }
        
        }
        
        QuickbooksConnector::server($map, $errmap);
        
        exit;
    }
}