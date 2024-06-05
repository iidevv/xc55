<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Customer;

use XLite\Controller\Customer\ACustomer;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Core\Converter;

class QbwcSyncProducts extends ACustomer
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
            QUICKBOOKS_ADD_INVENTORYITEM => array(
                [$object, 'qbcProductAddRequest'],
                [$object, 'qbcProductAddResponse'],
            ),
            QUICKBOOKS_QUERY_INVENTORYITEM => array(
                [$object, 'qbcProductQueryRequest'],
                [$object, 'qbcProductQueryResponse'],
            ),
            QUICKBOOKS_MOD_INVENTORYITEM => array(
                [$object, 'qbcProductModRequest'],
                [$object, 'qbcProductModResponse'],
            ),
        );

        $errmap = array(
            3100 => [$object, 'qbcProductAddResponse'],
            '*'  => [$object, 'qbcErrorCatchAll'],
        );
        
        if (empty(Session::getInstance()->qbwc_first_access_time)) {
            Session::getInstance()->qbwc_first_access_time = Converter::time();
        }
        
        if (Session::getInstance()->qbwc_first_access_time == Converter::time()) {
        
            QuickbooksConnector::qbcFilterQueue($map);

            $products = Database::getRepo('XLite\Model\OrderItem')
                ->getProductsForSync();

            if (!empty($products)) {

                foreach ($products as $p) {

                    $product_id = intval($p['product_id']);
                    $variant_id = intval($p['variant_id']);

                    if (!empty($variant_id)) {
                        $ID = $product_id . '_' . $variant_id;
                    } else {
                        $ID = $product_id;
                    }

                    $allowToAdd = (
                        empty($p['check'])
                        || (
                            $p['quickbooks_fullname']
                            && empty($p['quickbooks_listid'])
                        )
                    ) && QuickbooksConnector::allowAddProducts();

                    $allowToUpd = !empty($p['check'])
                        && empty($p['quickbooks_listid']);

                    if ($ID && $allowToAdd) {

                        // Adding product/variant
                        QuickbooksConnector::qbcQueueRequest(
                            QUICKBOOKS_ADD_INVENTORYITEM,
                            $ID
                        );

                    } elseif ($ID && $allowToUpd) {

                        // Existing product/variant, query request to get ListID
                        QuickbooksConnector::qbcQueueRequest(
                            QUICKBOOKS_QUERY_INVENTORYITEM,
                            $ID
                        );
                    }
                }
            }

            // Update prices

            if (QuickbooksConnector::updateProductPrices()) {

                $products = Database::getRepo(QuickbooksProducts::class)
                    ->getProductsWithUpdatedPrices();

                if (!empty($products)) {
                    foreach ($products as $p) {
                        $ID = $p['product_id']
                            . ($p['variant_id'] ? '_' . $p['variant_id'] : '');
                        QuickbooksConnector::qbcQueueRequest(
                            QUICKBOOKS_MOD_INVENTORYITEM,
                            $ID
                        );
                    }
                }
            }
        
        }
        
        QuickbooksConnector::server($map, $errmap);
        
        exit;
    }
}