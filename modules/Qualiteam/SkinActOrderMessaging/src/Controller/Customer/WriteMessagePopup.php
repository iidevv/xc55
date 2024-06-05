<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Customer;


use XLite\View\AView;

class WriteMessagePopup extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActOrderMessaging Write a message');
    }

    public function doActionGetOrderData() {
        $orderNumber = \XLite\Core\Request::getInstance()->orderNumber;
        $profile = \XLite\Core\Auth::getInstance()->getProfile();
        $data = [];

        if ($orderNumber && $profile) {
            $order = \XLite\Core\Database::getRepo('\XLite\Model\Order')->findOneBy([
                'orderNumber' => $orderNumber,
                'orig_profile' => $profile->getProfileId()
            ]);
            if ($order) {
                $items = [];
                foreach ($order->getItems() as $item) {
                    $items[] = [
                        'sku' => $item->getSKU(),
                        'name' => $item->getName(),
                        'amount' => $item->getAmount()
                    ];
                }
                $data = [
                    'orderNumber' => $order->getOrderNumber(),
                    'products' => $items,
                    'total' => AView::formatPrice($order->getTotal())
                ];
            }
        }

        $this->displayJSON(['data' => $data]);
        exit(0);
    }
}