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

class QbwcSyncCustomers extends ACustomer
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
            QUICKBOOKS_ADD_CUSTOMER => array(
                [$object, 'qbcCustomerAddRequest'],
                [$object, 'qbcCustomerAddResponse'],
            ),
            QUICKBOOKS_QUERY_CUSTOMER => array(
                [$object, 'qbcCustomerQueryRequest'],
                [$object, 'qbcCustomerQueryResponse'],
            )
        );

        $errmap = array(
            3100 => [$object, 'qbcCustomerAddResponse'],
            '*'  => [$object, 'qbcErrorCatchAll'],
        );
        
        if (empty(Session::getInstance()->qbwc_first_access_time)) {
            Session::getInstance()->qbwc_first_access_time = Converter::time();
        }
        
        if (Session::getInstance()->qbwc_first_access_time == Converter::time()) {
        
            QuickbooksConnector::qbcFilterQueue($map);

            $customers = Database::getRepo('XLite\Model\Order')
                ->getProfileIdsForSync();

            if (!empty($customers)) {
                foreach ($customers as $userid) {
                    if (!Database::getRepo('XLite\Model\Order')
                        ->checkProfileSynced($userid)) {
                        // Adding new customer
                        $action = QUICKBOOKS_ADD_CUSTOMER;
                    } else {
                        // Existing customer, query request to get ListID
                        $action = QUICKBOOKS_QUERY_CUSTOMER;
                    }
                    QuickbooksConnector::qbcQueueRequest($action, $userid);
                }
            }
        
        }
        
        QuickbooksConnector::server($map, $errmap);
        
        exit;
    }
}