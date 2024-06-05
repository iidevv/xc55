<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Mailer;
use Qualiteam\SkinActQuickbooks\Core\QuickbooksConnector;

class SendEmailOrdersErrorsMessage extends \XLite\Core\Mail\AMail
{
    protected static function defineVariables()
    {
        return parent::defineVariables();
    }

    public function __construct($body)
    {
        parent::__construct();
        
        if (is_array($body)) {
            
            $orderNumbers = [];
            
            foreach ($body as $orderNumber) {
                
                $url = Converter::buildFullURL(
                    'order',
                    '',
                    ['order_number' => $orderNumber],
                    \XLite::ADMIN_SELF
                );
                $orderNumbers[] = '<a href="' . $url . '">Order #'
                                . $orderNumber . '</a>';
                
            }
            
            $body = implode("\n", $orderNumbers);
        }
        
        $body = static::t('Qbc Orders Import Errors Text') . "\n\n" . $body;
        
        $vars = [
            'body' => $body,
        ];

        $this->populateVariables($vars);

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(QuickbooksConnector::sendEmailOrdersErrors());

        $this->appendData($vars);
    }
    
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActQuickbooks/orders_errors';
    }
}