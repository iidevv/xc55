<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks;

use Qualiteam\SkinActQuickbooks\View\Tabs\Quickbooks;
use XLite\Core\Converter;
use XLite\Module\AModule;

class Main extends AModule
{
    public const QBC_CUSTOMERS_LOG = 'qbc_customer';
    public const QBC_PRODUCTS_LOG  = 'qbc_product';
    public const QBC_ORDERS_LOG    = 'qbc_order';
    public const QBC_ERRORS_LOG    = 'qbc_error';
    
    public const LOG_NAME = 'quickbooks_connector';
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return Converter::buildURL(Quickbooks::TAB_GENERAL);
    }
    
    /**
     * Add log
     * 
     * @return void
     */
    public static function addLog()
    {
        $params = func_get_args();
        
        if (
            !empty($params[0])
            && in_array($params[0], [
                self::QBC_CUSTOMERS_LOG,
                self::QBC_PRODUCTS_LOG,
                self::QBC_ORDERS_LOG,
                self::QBC_ERRORS_LOG
                ])
        ) {
            
            $logName = array_shift($params);
            
        } else {
            
            $logName = self::LOG_NAME;
        }
        
        $filename = LC_DIR_LOG . '/' . date('Y') . '/' . date('m')
                  . '/' . $logName . '.' . date('Y-m-d') . '.log';
        
        if (!empty($params)) {
            
            $log = file_exists($filename)
                 ? file_get_contents($filename)
                 : '';
            
            $logTitle = '';
            
            if (is_string($params[0])) {
                $logTitle = ' ' . $params[0];
                unset($params[0]);
            }
            
            $log .= "[" . date('c') . "]" . ($logTitle ?? '') . ":\n";
            
            if (!empty($params)) {
                $log .= var_export($params, true) . "\n\n";
            }
            
            file_put_contents($filename, $log);
        }
    }
}