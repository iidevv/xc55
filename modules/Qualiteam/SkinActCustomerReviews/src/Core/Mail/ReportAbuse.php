<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Core\Mail;

use XLite\Core\Config;
use XLite\Core\Mailer;

class ReportAbuse extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/report_abuse';
    }

    public function __construct($review)
    {
        parent::__construct();

        /** @var \XC\Reviews\Model\Review $review */
        $profile = $review->getProfile();
        $product = $review->getProduct();

        $customerLink = \XLite\Core\Converter::buildURL('profile', '', ['profile_id' => $profile->getProfileId()], \XLite::getAdminScript());
        $customerName = $profile->getName();
        $productLink = \XLite\Core\Converter::buildURL('product', '', ['product_id' => $product->getProductId()], \XLite::getAdminScript());
        $productName = $product->getName();

        $reviewNumber = $review->getId();

        $reviewLink = \XLite\Core\Converter::buildURL('review', '', ['id' => $reviewNumber], \XLite::getAdminScript());

        $this->populateVariables([
            'customer_link' => $customerLink,
            'product_link' => $productLink,
            'review_number' => $reviewNumber,
            'customer_name' => $customerName,
            'product_name' => $productName,
            'review_link' => $reviewLink
        ]);

        $to = Config::getInstance()->XC->Reviews->abuse_email_address;

        $this->setTo($to);
        $this->setFrom(Mailer::getSiteAdministratorMail());
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'product_link' => '',
                'customer_link' => '',
                'review_number' => '',
                'customer_name' => '',
                'product_name' => '',
                'review_link' => '',
            ];
    }
}