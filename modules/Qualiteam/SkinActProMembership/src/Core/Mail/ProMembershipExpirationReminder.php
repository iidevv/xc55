<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core\Mail;


use XLite\Core\Converter;
use XLite\Core\Database;

class ProMembershipExpirationReminder extends \XLite\Core\Mail\AMail
{
    const DATE_FORMAT = '%Y/%m/%d';

    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActProMembership/pro_membership_expiration_reminder';
    }

    public static function isEnabled()
    {
        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);

        return $product && parent::isEnabled();
    }

    public function __construct(\XLite\Model\OrderItem $item, $daysNum, $expDate = '')
    {
        parent::__construct();

        if (!$item) {
            return;
        }

        $profile = $item->getOrder()->getProfile();

        $this->setFrom(\XLite\Core\Mailer::getOrdersDepartmentMail());

        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);

        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);

        $customerProductUrl = Converter::buildFullURL(
            'cart',
            'pro_membership_product',
            [
                'product_id' => $product->getProductId()
            ],
            'customer'
        );

        $this->populateVariables([
            'customer_product_url' => $customerProductUrl,
            'days' => $daysNum,
            'exp_date' => $expDate
        ]);
    }

    protected static function defineVariables()
    {
        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);
        $daysNum = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->days_before_expiration;

        return parent::defineVariables() + [
                'customer_product_url' => Converter::buildFullURL(
                    'cart',
                    'pro_membership_product',
                    [
                        'product_id' => $product->getProductId()
                    ],
                    'customer'
                ),
                'days' => $daysNum,
                'exp_date' => Converter::formatDate(null, static::DATE_FORMAT),
            ];
    }

}