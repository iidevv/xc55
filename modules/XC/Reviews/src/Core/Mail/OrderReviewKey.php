<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Core\Mail;

use XLite\Core\Mailer;
use XLite\Model\OrderItem;
use XLite\Model\Product;

class OrderReviewKey extends \XLite\Core\Mail\Order\AOrder
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return Mailer::NEW_REVIEW_KEY_NOTIFICATION;
    }

    /**
     * OrderReviewKey constructor.
     *
     * @param \XC\Reviews\Model\OrderReviewKey $reviewKey
     */
    public function __construct($reviewKey)
    {
        $order = $reviewKey->getOrder();

        parent::__construct($order);

        $profile = $order->getProfile();

        $this->setLanguageCode($profile->getLanguage());
        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
        $this->setReplyTo(Mailer::getOrdersDepartmentMails());

        $this->appendData([
            'products'       => $this->getProducts($order),
            'recipient_name' => $profile->getName(),
            'companyName'    => \XLite\Core\Config::getInstance()->Company->company_name,
            'orderNumber'    => $order->getOrderNumber(),
            'orderDate'      => $order->getDate(),
            'urlProcessor'   => static function (Product $product) use ($reviewKey) {
                return \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'product',
                        '',
                        [
                            'product_id' => $product->getProductId(),
                            'rkey'       => $reviewKey->getKeyValue(),
                        ],
                        \XLite::getCustomerScript()
                    )
                );
            },
        ]);

        $this->populateVariables([
            'recipient_name' => $profile->getName(),
        ]);
    }

    /**
     * Get list of products for notification
     *
     * @param Order $order
     * @return array
     */
    protected function getProducts($order)
    {
        $result = [];

        foreach ($order->getItems() as $item) {
            if ($this->isOrderItemSuitableForReviewKeyNotification($item)) {
                $result[] = $item->getProduct();
            }
        }

        return $result;
    }

    /**
     * Return true if item is valid for notification
     *
     * @param OrderItem $item
     * @return boolean
     */
    protected function isOrderItemSuitableForReviewKeyNotification($item)
    {
        return !$item->isDeleted() && $item->getProduct()->isAvailable();
    }
}
