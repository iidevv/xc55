<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Mail\Profile\AProfile;
use XLite\Model\Repo\ARepo;
use XLite\Model\Repo\Product;

class BuyProMembership extends AProfile
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActProMembership/pro_membership';
    }

    public function __construct(\XLite\Model\Profile $profile, \XLite\Model\Product $product)
    {
        parent::__construct($profile);

        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);

        $customerProductUrl = Converter::buildFullURL(
            'cart',
            'pro_membership_product',
            [
                'product_id' => $product->getProductId()
            ],
            'customer'
        );

        $this->populateVariables(['customer_product_url' => $customerProductUrl]);
    }

    protected static function defineVariables()
    {
        $cnd                       = new \XLite\Core\CommonCell;
        $cnd->{Product::P_ENABLED} = true;
        $cnd->{ARepo::P_LIMIT}     = [1];

        return parent::defineVariables() + [
            'customer_product_url' => Converter::buildFullURL(
                'cart',
                'pro_membership_product',
                [
                    'product_id' => Database::getRepo('XLite\Model\Product')->search($cnd),
                ],
                'customer'
            ),
        ];
    }
}