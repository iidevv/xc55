<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Core\Mail;

use XLite\Core\Mailer;
use XC\CanadaPost\Model\ProductsReturn;
use XC\CanadaPost\Model\ProductsReturn\Item;

class ProductsReturnApproved extends \XLite\Core\Mail\Order\AOrder
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/XC/CanadaPost/return_approved';
    }

    public function __construct(ProductsReturn $return)
    {
        parent::__construct($return->getOrder());

        if (
            $return->getOrder()
            && $profile = $return->getOrder()->getProfile()
        ) {
            $this->setFrom(Mailer::getOrdersDepartmentMail());
            $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
            $this->setReplyTo(Mailer::getOrdersDepartmentMails());
            $this->tryToSetLanguageCode($profile->getLanguage());

            $this->appendData([
                'productsReturn' => $return,
                'notes'          => nl2br($return->getAdminNotes(), false),
                'products' => array_map(static function (Item $item) {
                    return $item->getOrderItem()->getProduct();
                }, $return->getItems()->toArray())
            ]);
        }
    }

    public function send()
    {
        return !empty($this->getData()['productsReturn']) && parent::send();
    }
}
