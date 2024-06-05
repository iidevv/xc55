<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core\Task;

use XLite\Core\Converter;
use XLite\Core\Mailer;

class ProMembershipExpirationReminder extends \XLite\Core\Task\Base\Periodic
{

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Pro membership expiration reminder';
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        error_reporting(E_ALL & ~E_WARNING);

        $daysNum = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->days_before_expiration;

        if ($daysNum > 0) {

            /** @var \Qualiteam\SkinActProMembership\Model\Repo\OrderItem $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\OrderItem');
            //$daysNum = 1000;

            $items = $repo->getItemsWithExpiredMembershipsInNextDays($daysNum);

            $time = \XLite\Core\Converter::time();

            if ($items) {

                foreach ($items as $item) {

                    $expDate = Converter::formatDate($item->getCustomerMembershipUnassignDate(),
                        \Qualiteam\SkinActProMembership\Core\Mail\ProMembershipExpirationReminder::DATE_FORMAT);

                    Mailer::sendNotificationProMembershipExpirationReminder($item, $daysNum, $expDate);

                    $item->setCustomerMembershipExpirationSentDate($time);
                }

            }

        }

    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::INT_1_HOUR;
    }
}
