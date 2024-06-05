<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\Controller\Customer;


use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;

class ReportAbuse extends \XLite\Controller\Customer\ACustomer
{

    public function getTitle()
    {
        return static::t('SkinActCustomerReviews Report abuse');
    }

    protected function doActionReport()
    {
        $review = Database::getRepo('\XC\Reviews\Model\Review')->find((int)Request::getInstance()->rid);

        if ($review) {
            \XLite\Core\Mailer::sendNotificationAbuseReport($review);
            TopMessage::addInfo('SkinActCustomerReviews successfully sent');
        } else {
            TopMessage::addInfo('SkinActCustomerReviews submission problem');
        }

    }
}