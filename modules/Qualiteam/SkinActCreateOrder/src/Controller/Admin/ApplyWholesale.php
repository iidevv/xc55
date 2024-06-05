<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;


use XLite\Core\Database;
use XLite\Core\Request;

class ApplyWholesale extends \XLite\Controller\Admin\AAdmin
{

    protected function doActionApply()
    {
        $orderId = Request::getInstance()->order_id;
        $order = Database::getRepo('\XLite\Model\Order')->find($orderId);

        $productId = Request::getInstance()->product_id;
        $product = Database::getRepo('\XLite\Model\Product')->find($productId);

        $amount = (int)Request::getInstance()->amount;

        if ($order && $product && $amount > 0) {
            $membership = $order->getProfile() ? $order->getProfile()->getMembership() : false;

            $product->setWholesaleQuantity($amount);
            $product->setWholesaleMembership($membership);

            $wholesalePrice = $product->getWholesalePrice($membership);

            if (!$wholesalePrice) {
                $price = $product->getPrice();
            } else {
                $price = $wholesalePrice;
            }

            $price = \XLite\View\AView::formatPrice($price);
            $currency = \XLite::getInstance()->getCurrency();
            $price = str_replace([$currency->getPrefix(), $currency->getSuffix()], '', $price);
            exit(\json_encode(['wsp' => $price]));
        }

        exit(\json_encode(['wsp' => 0]));
    }

    public static function defineFreeFormIdActions()
    {
        return ['apply'];
    }
}