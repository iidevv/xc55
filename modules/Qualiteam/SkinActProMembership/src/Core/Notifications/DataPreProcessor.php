<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core\Notifications;

use Qualiteam\SkinActProMembership\Core\Mail\ProMembershipExpirationReminder;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;
use XLite\Model\Repo\Product;

/**
 * DataPreProcessor
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XC\ThemeTweaker\Core\Notifications\DataPreProcessor
{
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        if ($dir === 'modules/Qualiteam/SkinActProMembership/pro_membership_expiration_reminder') {
            $data = static::getDemoProMembershipExpirationReminder();
        }

        if ($dir === 'modules/Qualiteam/SkinActProMembership/pro_membership') {
            $data = static::getDemoProMembership();
        }

        return $data;
    }

    protected static function getDemoProMembershipExpirationReminder()
    {
        $orderItem = Database::getRepo('\XLite\Model\OrderItem')->findOneBy(['item_id' > 0]);

        $daysNum = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->days_before_expiration;

        return [
            $orderItem,
            $daysNum,
            Converter::formatDate(null, ProMembershipExpirationReminder::DATE_FORMAT),
        ];
    }

    protected static function getDemoProMembership()
    {
        $profile = Auth::getInstance()->getProfile();

        $cnd                       = new \XLite\Core\CommonCell;
        $cnd->{ARepo::P_LIMIT}     = [1];
        $cnd->{Product::P_ENABLED} = true;

        $product = Database::getRepo('XLite\Model\Product')->search($cnd)[0];

        return [
            $profile,
            $product,
        ];
    }

}