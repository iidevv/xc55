<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\Product;

class LowVariantLimitWarningAdmin extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'modules/XC/ProductVariants/low_variant_limit_warning';
    }

    protected static function defineVariables()
    {
        return parent::defineVariables() + [
                'product_name' => '',
            ];
    }

    public function __construct($data)
    {
        parent::__construct();

        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo(Mailer::getSiteAdministratorMails());
        $this->setReplyTo(Mailer::getOrdersDepartmentMails());

        $this->populateVariables([
            'product_name' => $data['product']->getName(),
        ]);

        $this->appendData([
            'product'      => $data['product'],
            'amount'       => $data['amount'],
            'data'         => $data,
            'urlProcessor' => static function (Product $product) {
                return Converter::buildFullURL(
                    'product',
                    '',
                    [
                        'product_id' => $product->getId(),
                        'page'       => 'variants',
                    ],
                    \XLite::getAdminScript()
                );
            },
        ]);
    }
}
