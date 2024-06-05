<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Request;
use Qualiteam\SkinActQuickbooks\Main as QBMain;

class QuickbooksQwcFiles extends Quickbooks
{
    const OPTIONS = [];
    
    private $qwc_files = array(
        'sync_customers' => array(
            'name'     => 'Customers - X-Cart to QuickBooks',
            'desc'     => 'Push X-Cart customers to QuickBooks',
            'filename' => 'customers-xcart-to-quickbooks-wc-file.qwc',
            'comment'  => 'QWC File for customers (X-Cart to QuickBooks)',
        ),
        'sync_products' => array(
            'name'     => 'Products - X-Cart to QuickBooks',
            'desc'     => 'Push X-Cart products to QuickBooks',
            'filename' => 'products-xcart-to-quickbooks-wc-file.qwc',
            'comment'  => 'QWC File for products (X-Cart to QuickBooks)',
        ),
        'sync_orders'   => array(
            'name'     => 'Orders - X-Cart to QuickBooks',
            'desc'     => 'Push X-Cart orders to QuickBooks',
            'filename' => 'orders-xcart-to-quickbooks-wc-file.qwc',
            'comment'  => 'QWC File for orders (X-Cart to QuickBooks)',
        ),
    );
    
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            ['make_qwc_file']
        );
    }
    
    /**
     * Make QWC file action
     * 
     * @return void
     */
    public function doActionMakeQwcFile()
    {
        $qwcFile = Request::getInstance()->qwc_file;
        if (
            !empty($qwcFile)
            && isset($this->qwc_files[$qwcFile])
        ) {
            $qwcFileData = $this->qwc_files[$qwcFile];
            $username = Config::getInstance()->Qualiteam->SkinActQuickbooks->qbc_user;
            $name = $qwcFileData['name'];
            $desc = $qwcFileData['desc'];
            $appurl = Converter::buildFullURL('qbwc_' . $qwcFile, '', array(), \XLite::CUSTOMER_INTERFACE);
            $appsupport = Converter::buildFullURL('', '', array(), \XLite::CUSTOMER_INTERFACE);
            $fileid = $this->generateQwcGuid();
            $ownerid = $this->generateQwcGuid();
            $filename = $qwcFileData['filename'];
            
            $xml =<<<XML
<?xml version="1.0"?>
<QBWCXML>
    <AppName>{$name}</AppName>
    <AppID></AppID>
    <AppURL>{$appurl}</AppURL>
    <AppDescription>{$desc}</AppDescription>
    <AppSupport>{$appsupport}</AppSupport>
    <UserName>{$username}</UserName>
    <OwnerID>{$ownerid}</OwnerID>
    <FileID>{$fileid}</FileID>
    <QBType>QBFS</QBType>
    <Notify>false</Notify>
    <Scheduler>
        <RunEveryNMinutes>5</RunEveryNMinutes>
    </Scheduler>
    <IsReadOnly>false</IsReadOnly>
</QBWCXML>
XML;

            QBMain::addLog([__FUNCTION__, $xml]);

            header('Content-type: text/xml');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            print($xml);

            exit;
        }
    }
    
    /**
     * Generate OwnerID/FileID values for .QWC file
     * 
     * @return string
     */
    private function generateQwcGuid()
    {
       if (function_exists('com_create_guid') === true) {
           return com_create_guid();
       }
       return '{' . sprintf(
           '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
           mt_rand(0, 65535),
           mt_rand(0, 65535),
           mt_rand(0, 65535),
           mt_rand(16384, 20479),
           mt_rand(32768, 49151),
           mt_rand(0, 65535),
           mt_rand(0, 65535),
           mt_rand(0, 65535)
       ) . '}';
    }
}