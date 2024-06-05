<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Core\Task;

use Qualiteam\SkinActQuickbooks\Main as QBMain;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrderErrors;
use XLite\Core\Database;
use XLite\Core\Mailer;

class SendEmailOrdersErrors extends \XLite\Core\Task\Base\Periodic
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Send email about errors in orders (Quickbooks Connector)';
    }
    
    /**
     * @return integer
     */
    protected function getPeriod()
    {
        return self::INT_1_DAY;
    }
    
    /**
     * Check - task ready or not
     *
     * @return boolean
     */
    public function isReady()
    {
        if (QuickbooksConnector::sendEmailOrdersErrors()) {
            return true;
        }
        
        return false;
    }

    /**
     * Run task
     * 
     * @return void
     */
    protected function runStep()
    {
        $orderNumbers = Database::getRepo(QuickbooksOrderErrors::class)
            ->getErrorOrderNumbers();
        
        if ($orderNumbers) {
            
            Mailer::sendEmailAboutOrdersImportErrors($orderNumbers);
            
            Database::getRepo(QuickbooksOrderErrors::class)
            ->markOrdersErrorsAsSent();
            
            $info = 'Send notification about orders with errors to "'
                  . QuickbooksConnector::sendEmailOrdersErrors() . '":' . "\n";
            $info .= implode("\n", $orderNumbers);
            
        } else {
            
            $info = 'There are no orders with errors';
        }
        
        QBMain::addLog(__FUNCTION__, $info);
    }
}